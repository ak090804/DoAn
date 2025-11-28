<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class BuildApriori extends Command
{
    protected $signature = 'recommendations:apriori
        {--min_support=0.005 : Minimum support (Độ hỗ trợ tối thiểu)}
        {--min_confidence=0.2 : Minimum confidence (Độ tin cậy tối thiểu)}
        {--min_lift=3 : Minimum lift (Độ nâng tối thiểu)}
        {--top=0 : Limit number of rules (0 là lấy tất cả)}';

    protected $description = 'Kết hợp dữ liệu CSV mẫu và Database để chạy thuật toán Apriori';

    public function handle()
    {
        $this->info('=============================================');
        $this->info('Starting Apriori Recommendation Build Process');
        $this->info('=============================================');

        // 1. CẤU HÌNH ĐƯỜNG DẪN
        $seedPath = storage_path('app/seed_transactions.csv');
        $processPath = storage_path('app/orders_final.csv');
        $outputJson = storage_path('app/apriori_rules.json');

        // 2. CHUẨN BỊ FILE DỮ LIỆU
        $this->info('Step 1: Preparing data files...');
        if (File::exists($seedPath)) {
            File::copy($seedPath, $processPath);
            $this->info("✔ Loaded seed data from: $seedPath");
        } else {
            file_put_contents($processPath, "");
            $this->warn("⚠ Seed file not found. Created empty file.");
        }

        // Lấy transactions từ DB
        $this->info('Step 2: Fetching transactions from Database...');
        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->select('orders.id', DB::raw('GROUP_CONCAT(product_variants.product_id) as products'))
            ->groupBy('orders.id')
            ->get();

        // --- Tìm số cột lớn nhất để padding ---
        $maxCols = 0;
        if (File::exists($processPath)) {
            $lines = file($processPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $cols = explode(",", $line);
                $maxCols = max($maxCols, count($cols));
            }
        }

        if ($maxCols === 0 && !$rows->isEmpty()) {
            foreach ($rows as $r) {
                $cols = explode(",", $r->products);
                $maxCols = max($maxCols, count($cols));
            }
        }

        if (!$rows->isEmpty()) {
            $fp = fopen($processPath, 'a');
            $count = 0;
            foreach ($rows as $r) {
                $items = explode(",", trim($r->products));
                $padCount = max(0, $maxCols - count($items));
                if ($padCount > 0) {
                    $items = array_merge($items, array_fill(0, $padCount, ""));
                }
                fwrite($fp, implode(",", $items) . "\n");
                $count++;
            }
            fclose($fp);
            $this->info("✔ Appended $count transactions from Database to processing file (padded to $maxCols columns).");
        } else {
            $this->warn("⚠ No transactions found in Database to append.");
        }

        $this->info("Data ready at: $processPath");

        // 3. CHẠY SCRIPT PYTHON
        $this->info('Step 3: Running Python Script...');
        $python = env('PYTHON_PATH', 'python'); // nếu không có env thì fallback 'python'
        $script = base_path('scripts/apriori_build.py');

        if (!file_exists($script)) {
            $this->error("❌ Python script not found at: $script");
            return Command::FAILURE;
        }

        $cmd = "$python " . escapeshellarg($script)
            . " " . escapeshellarg($processPath)
            . " " . escapeshellarg($outputJson)
            . " --min_support " . escapeshellarg($this->option('min_support'))
            . " --min_confidence " . escapeshellarg($this->option('min_confidence'))
            . " --min_lift " . escapeshellarg($this->option('min_lift'))
            . " --top " . escapeshellarg($this->option('top'));

        $output = [];
        $returnVar = 0;
        exec($cmd . ' 2>&1', $output, $returnVar);

        foreach ($output as $line) {
            $this->line("   [Python] $line");
        }

        if ($returnVar !== 0 || !file_exists($outputJson)) {
            $this->error("❌ Python script failed or output JSON not found.");
            return Command::FAILURE;
        }

        // 4. IMPORT KẾT QUẢ VÀO DATABASE
        $this->info('Step 4: Importing rules to Database...');
        $rules = json_decode(file_get_contents($outputJson), true);

        if (!is_array($rules)) {
            $this->error("❌ Invalid JSON output.");
            return Command::FAILURE;
        }

        if (!Schema::hasTable('apriori_recommendations')) {
            $this->error("❌ Table 'apriori_recommendations' does not exist.");
            return Command::FAILURE;
        }

        DB::beginTransaction();
        try {
            DB::table('apriori_recommendations')->delete();

            $seen = [];
            $insertData = [];

            foreach ($rules as $r) {
                $antecedents = (array) $r['antecedent']; // ép về array nếu là số hoặc tuple
                $consequents = (array) $r['consequent'];

                foreach ($antecedents as $a) {
                    foreach ($consequents as $c) {
                        $key = $a.'-'.$c;
                        if (isset($seen[$key])) continue;
                        $seen[$key] = true;

                        $insertData[] = [
                            'product_id' => intval($a),
                            'recommended_product_id' => intval($c),
                            'score' => floatval($r['confidence'] ?? 0),
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            // Insert theo batch 500 dòng
            $batchSize = 500;
            for ($i = 0; $i < count($insertData); $i += $batchSize) {
                DB::table('apriori_recommendations')->insert(array_slice($insertData, $i, $batchSize));
            }

            DB::commit();
            $this->info("✔ Successfully imported " . count($insertData) . " unique rules.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }



        $this->info('=============================================');
        $this->info('PROCESS COMPLETED SUCCESSFULLY');
        return Command::SUCCESS;
    }
}
