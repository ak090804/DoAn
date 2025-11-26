<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class UpdateRecommendationsForOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * For each product_variant in the order we compute top co-purchased
     * other variants (by SUM(quantity) across orders) and upsert into
     * `product_recommendations` table.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (!Schema::hasTable('product_recommendations')) {
                return;
            }

            // get distinct variants in this order
            $variants = DB::table('order_items')
                ->where('order_id', $this->orderId)
                ->distinct()
                ->pluck('product_variant_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (empty($variants)) {
                return;
            }

            foreach ($variants as $v) {
                $rows = DB::table('order_items as oi1')
                    ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                    ->where('oi1.product_variant_id', $v)
                    ->where('oi2.product_variant_id', '<>', $v)
                    ->select('oi2.product_variant_id', DB::raw('SUM(oi2.quantity) as score'))
                    ->groupBy('oi2.product_variant_id')
                    ->orderByDesc('score')
                    ->limit(10)
                    ->get();

                foreach ($rows as $r) {
                    DB::table('product_recommendations')->updateOrInsert([
                        'product_variant_id' => $v,
                        'recommended_variant_id' => $r->product_variant_id,
                    ], [
                        'score' => $r->score,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $ex) {
            Log::error('UpdateRecommendationsForOrder failed: ' . $ex->getMessage());
        }
    }
}
