<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class BuildRecommendations extends Command
{
    protected $signature = 'recommendations:build {--min_support=0.02} {--min_confidence=0.3} {--top=0}';
    protected $description = 'Export transactions and run FP-Growth (python script) to build product recommendations';

    public function handle()
    {
        $this->info('Exporting transactions...');

        $transactionsPath = base_path('storage/app/recommendations_transactions.csv');
        $outputJson = base_path('storage/app/recommendation_rules.json');

        // Export orders -> transactions (product_variant_id per order)
        $rows = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('orders.id as order_id', DB::raw('GROUP_CONCAT(order_items.product_variant_id) as variants'))
            ->groupBy('orders.id')
            ->get();

        $fp = fopen($transactionsPath, 'w');
        foreach ($rows as $r) {
            // GROUP_CONCAT uses comma, ensure no spaces
            fwrite($fp, $r->variants . "\n");
        }
        fclose($fp);

        $this->info('Transactions exported to: ' . $transactionsPath);

        // Call Python script
        $python = 'python';
        $script = base_path('scripts/fpgrowth_build.py');
        $cmd = escapeshellcmd($python) . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($transactionsPath) . ' ' . escapeshellarg($outputJson) .
            ' --min_support ' . escapeshellarg($this->option('min_support')) . ' --min_confidence ' . escapeshellarg($this->option('min_confidence')) . ' --top ' . escapeshellarg($this->option('top'));

        $this->info('Running FP-Growth script...');
        $this->info($cmd);
        $output = [];
        $ret = 0;
        exec($cmd . ' 2>&1', $output, $ret);
        foreach ($output as $line) {
            $this->line($line);
        }

        if ($ret !== 0) {
            $this->error('FP-Growth script failed. Please ensure Python 3 and mlxtend are installed.');
            return 1;
        }

        if (!file_exists($outputJson)) {
            $this->error('Output JSON not found: ' . $outputJson);
            return 1;
        }

        $this->info('Importing rules into DB...');
        $rules = json_decode(file_get_contents($outputJson), true);
        if (!is_array($rules)) {
            $this->error('Invalid rules JSON');
            return 1;
        }

        // Ensure table exists before importing
        if (!Schema::hasTable('product_recommendations')) {
            $this->error('Table `product_recommendations` does not exist. Run migrations first.');
            return 1;
        }

        // Insert pairs into product_recommendations
        DB::beginTransaction();
        try {
            // Use delete() instead of truncate() so transaction behavior is consistent
            DB::table('product_recommendations')->delete();
            foreach ($rules as $r) {
                $ants = $r['antecedent'];
                $cons = $r['consequent'];
                $score = $r['confidence'] ?? ($r['support'] ?? 0);
                foreach ($ants as $a) {
                    foreach ($cons as $c) {
                        DB::table('product_recommendations')->updateOrInsert([
                            'product_variant_id' => $a,
                            'recommended_variant_id' => $c,
                        ], ['score' => $score, 'created_at' => now(), 'updated_at' => now()]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            try {
                DB::rollBack();
            } catch (\Exception $rollbackEx) {
                // ignore rollback errors when no active transaction
            }
            $this->error('Failed to import rules: ' . $ex->getMessage());
            return 1;
        }

        $this->info('Recommendations built and saved to `product_recommendations`.');
        return 0;
    }
}
