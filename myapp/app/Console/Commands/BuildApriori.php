<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class BuildApriori extends Command
{
    protected $signature = 'recommendations:apriori
        {--min_support=0.005 : Minimum support}
        {--min_confidence=0.2 : Minimum confidence}
        {--min_lift=3 : Minimum lift}
        {--top=3 : Số lượng gợi ý tối đa cho mỗi sản phẩm (0 = không giới hạn)}';

    protected $description = 'Chạy Apriori + chỉ giữ tối đa N gợi ý mạnh nhất cho mỗi sản phẩm';

    public function handle()
    {
        $this->info('=============================================');
        $this->info('Starting Apriori Recommendation Build Process');
        $this->info('=============================================');

        // 1. CẤU HÌNH
        $seedPath    = storage_path('app/seed_transactions.csv');
        $processPath = storage_path('app/orders_final.csv');
        $outputJson  = storage_path('app/apriori_rules.json');
        $maxPerAntecedent = (int) $this->option('top'); // <-- Đây chính là giới hạn

        // 2. CHUẨN BỊ FILE CSV (giữ nguyên như cũ)
        $this->info('Step 1: Preparing data files...');
        if (File::exists($seedPath)) {
            File::copy($seedPath, $processPath);
            $this->info("Loaded seed data from: $seedPath");
        } else {
            file_put_contents($processPath, "");
            $this->warn("Seed file not found. Created empty file.");
        }

        // Lấy dữ liệu từ DB và append vào CSV (giữ nguyên logic cũ của bạn)
        $this->info('Step 2: Fetching transactions from Database...');
        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->select('orders.id', DB::raw('GROUP_CONCAT(product_variants.product_id ORDER BY product_variants.product_id) as products'))
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
            $count = 0;
            foreach ($rows as $r) {
                $items = array_filter(explode(',', trim($r->products)));
                $padCount = max(0, $maxCols - count($items));
                if ($padCount > 0) {
                    $items = array_pad($items, $maxCols, "");
                }
                fwrite($fp, implode(",", $items) . "\n");
                $count++;
            }
            fclose($fp);
            $this->info("Appended $count transactions from DB.");
        }

        // 3. CHẠY PYTHON (giữ nguyên)
        $this->info('Step 3: Running Python Apriori script...');
        $python = env('PYTHON_PATH', 'python');
        $script = base_path('scripts/apriori_build.py');

        if (!file_exists($script)) {
            $this->error("Python script not found: $script");
            return Command::FAILURE;
        }

        $cmd = "$python " . escapeshellarg($script)
            . " " . escapeshellarg($processPath)
            . " " . escapeshellarg($outputJson)
            . " --min_support " . $this->option('min_support')
            . " --min_confidence " . $this->option('min_confidence')
            . " --min_lift " . $this->option('min_lift')
            . " --top 0"; // <-- Python xuất hết, PHP sẽ lọc

        $output = [];
        $returnVar = 0;
        exec($cmd . ' 2>&1', $output, $returnVar);

        foreach ($output as $line) $this->line("   [Python] $line");

        if ($returnVar !== 0 || !file_exists($outputJson)) {
            $this->error("Python script failed!");
            return Command::FAILURE;
        }

        // 4. ĐỌC + LỌC TOP N CHO MỖI ANTECEDENT + IMPORT
        $this->info('Step 4: Filtering top rules & importing to DB...');
        $rawRules = json_decode(file_get_contents($outputJson), true);

        if (!is_array($rawRules)) {
            $this->error("Invalid JSON output");
            return Command::FAILURE;
        }

        // Gom nhóm theo antecedent + sắp xếp theo confidence
        $grouped = [];
        foreach ($rawRules as $r) {
            $ante = trim($r['antecedent'] ?? '');
            $cons = trim($r['consequent'] ?? '');
            $conf = $r['confidence'] ?? 0;

            if (!$ante || !$cons) continue;

            $grouped[$ante][] = [
                'antecedent' => $ante,
                'consequent' => $cons,
                'confidence' => (float) $conf,
                'support' => $r['support'] ?? 0,
                'lift' => $r['lift'] ?? 1,
            ];
        }

        // Lấy top N cho mỗi antecedent
        $finalRules = [];
        foreach ($grouped as $ante => $list) {
            // Sắp xếp giảm dần confidence
            usort($list, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

            $limit = ($maxPerAntecedent > 0) ? $maxPerAntecedent : count($list);
            foreach (array_slice($list, 0, $limit) as $rule) {
                $finalRules[] = $rule;
            }
        }

        // Import vào DB (đã đảm bảo mỗi antecedent chỉ có tối đa N bản ghi)
        if (!Schema::hasTable('apriori_recommendations')) {
            $this->error("Table 'apriori_recommendations' does not exist.");
            return Command::FAILURE;
        }

        DB::beginTransaction();
        try {
            DB::table('apriori_recommendations')->truncate(); // hoặc delete()

            $insertData = [];
            $seen = [];

            foreach ($finalRules as $r) {
                $a = $r['antecedent'];
                $c = $r['consequent'];
                $key = "$a-$c";
                if (isset($seen[$key])) continue;
                $seen[$key] = true;

                $insertData[] = [
                    'product_id' => (int) $a,
                    'recommended_product_id' => (int) $c,
                    'score' => (float) $r['confidence'],
                    'support' => (float) ($r['support'] ?? 0),
                    'lift' => (float) ($r['lift'] ?? 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert batch
            $batchSize = 500;
            foreach (array_chunk($insertData, $batchSize) as $chunk) {
                DB::table('apriori_recommendations')->insert($chunk);
            }

            DB::commit();

            $this->info("HOÀN TẤT!");
            $this->info("→ Đã import " . count($insertData) . " luật gợi ý");
            $this->info("→ Mỗi sản phẩm có tối đa " . ($maxPerAntecedent > 0 ? $maxPerAntecedent : "không giới hạn") . " gợi ý mạnh nhất");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('=============================================');
        $this->info('PROCESS COMPLETED SUCCESSFULLY');
        return Command::SUCCESS;
    }
}