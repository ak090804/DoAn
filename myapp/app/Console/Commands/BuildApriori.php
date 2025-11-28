<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class BuildApriori extends Command
{
    protected $signature = 'recommendations:apriori
        {--min_support=0.005}
        {--min_confidence=0.2}
        {--min_lift=3}
        {--top=3 : Số gợi ý tối đa mỗi sản phẩm (0 = không giới hạn)}';

    protected $description = 'Build Apriori 1→1 rules with top-N filtering';

    public function handle()
    {
        $this->info('=== APRIORI START ===');

        // File paths
        $seedPath = storage_path('app/seed_transactions.csv');
        $processPath = storage_path('app/orders_final.csv');
        $outputJson = storage_path('app/apriori_rules.json');
        $maxPerAntecedent = (int)$this->option('top');

        // --- Step 1: Prepare seed file ---
        $this->info('Preparing CSV…');
        if (File::exists($seedPath)) {
            File::copy($seedPath, $processPath);
        } else {
            file_put_contents($processPath, "");
        }

        // --- Step 2: Append DB orders ---
        $this->info('Fetching DB transactions…');

        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->select('orders.id', DB::raw('GROUP_CONCAT(product_variants.product_id ORDER BY product_variants.product_id) AS products'))
            ->groupBy('orders.id')
            ->get();

        $maxCols = 0;
        if (File::exists($processPath)) {
            foreach (file($processPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $maxCols = max($maxCols, count(explode(',', $line)));
            }
        }

        if (!$rows->isEmpty()) {
            $fp = fopen($processPath, 'a');

            // Kiểm tra dòng cuối của file hiện tại
            $fileContent = file($processPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!empty($fileContent)) {
                $lastLine = end($fileContent);
                // Nếu dòng cuối không kết thúc bằng newline, thêm một dòng trống
                fseek($fp, 0, SEEK_END); // đảm bảo con trỏ ở cuối file
                fwrite($fp, "\n");
            }

            foreach ($rows as $r) {
                $items = array_filter(explode(',', trim($r->products)));
                if ($maxCols > 0) {
                    $items = array_pad($items, $maxCols, "");
                }
                fwrite($fp, implode(",", $items) . "\n");
            }
            fclose($fp);
        }


        // --- Step 3: Run Python ---
        $this->info('Running Python Apriori…');
        $python = env('PYTHON_PATH', 'python');
        $script = base_path('scripts/apriori_build.py');

        $cmd = "$python " . escapeshellarg($script)
            . " " . escapeshellarg($processPath)
            . " " . escapeshellarg($outputJson)
            . " --min_support " . $this->option('min_support')
            . " --min_confidence " . $this->option('min_confidence')
            . " --min_lift " . $this->option('min_lift')
            . " --top 0";

        $output = [];
        $returnVar = 0;
        exec($cmd . " 2>&1", $output, $returnVar);
        foreach ($output as $line) $this->line("[Python] $line");

        if (!file_exists($outputJson)) {
            $this->error("Python script failed!");
            return Command::FAILURE;
        }

        // --- Step 4: Load JSON ---
        $rawRules = json_decode(file_get_contents($outputJson), true);

        if (!is_array($rawRules)) {
            $this->error("Invalid JSON. Abort.");
            return Command::FAILURE;
        }

        // --- FIX: Support BOTH formats ---
        $grouped = [];
        foreach ($rawRules as $r) {

            $a = $r['product_id']
                ?? $r['antecedent']
                ?? null;

            $c = $r['recommended_product_id']
                ?? $r['consequent']
                ?? null;

            if (!$a || !$c) continue;

            $grouped[$a][] = [
                'product_id' => (int)$a,
                'recommended_product_id' => (int)$c,
                'confidence' => (float)($r['confidence'] ?? 0),
                'support' => (float)($r['support'] ?? 0),
                'lift' => (float)($r['lift'] ?? 1),
            ];
        }

        // --- Step 5: Filter top-N ---
        $finalRules = [];
        foreach ($grouped as $pid => $list) {
            usort($list, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
            $limit = $maxPerAntecedent > 0 ? $maxPerAntecedent : count($list);
            $finalRules = array_merge($finalRules, array_slice($list, 0, $limit));
        }

        // --- Step 6: Import ---
        if (!Schema::hasTable('apriori_recommendations')) {
            $this->error("Missing table apriori_recommendations");
            return Command::FAILURE;
        }

        // FIX: Truncate outside transaction
        DB::table('apriori_recommendations')->truncate();

        try {
            DB::beginTransaction();

            foreach (array_chunk($finalRules, 300) as $chunk) {
                DB::table('apriori_recommendations')->insert($chunk);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("=== DONE ===");
        $this->info("Imported: " . count($finalRules) . " rules");

        return Command::SUCCESS;
    }
}
