<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\OrderItems;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Schema;

class RecommendationService
{
    /**
     * Recommend product variants for a given customer using simple co-purchase counts.
     * This approximates FP-Growth by returning items most frequently bought together
     * with the items the customer already purchased.
     *
     * @param int $customerId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function recommendForCustomer(int $customerId, int $limit = 10)
    {
        // 1) Get distinct product_variant_ids the customer has purchased
        $purchased = OrderItems::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customerId)
            ->distinct()
            ->pluck('order_items.product_variant_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($purchased)) {
            return collect();
        }

        // 2) First, try to recommend products the customer buys most often (personalized)
        $userTop = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customerId)
            ->select('order_items.product_variant_id', DB::raw('SUM(order_items.quantity) as score'))
            ->groupBy('order_items.product_variant_id')
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        $userTopIds = $userTop->pluck('product_variant_id')->filter()->unique()->toArray();
        if (!empty($userTopIds)) {
            $variants = ProductVariant::with('product')
                ->whereIn('id', $userTopIds)
                ->get()
                ->sortBy(function ($v) use ($userTopIds) {
                    return array_search($v->id, $userTopIds);
                })
                ->values();

            return $variants;
        }

        // 3) Prefer precomputed FP-Growth recommendations if table exists
        if (Schema::hasTable('product_recommendations')) {
            $rows = DB::table('product_recommendations')
                ->whereIn('product_variant_id', $purchased)
                ->orderByDesc('score')
                ->limit($limit)
                ->get();

            $recommendedIds = $rows->pluck('recommended_variant_id')->filter()->unique()->toArray();

            if (!empty($recommendedIds)) {
                $variants = ProductVariant::with('product')
                    ->whereIn('id', $recommendedIds)
                    ->get()
                    ->sortBy(function ($v) use ($recommendedIds) {
                        return array_search($v->id, $recommendedIds);
                    })
                    ->values();

                return $variants;
            }
        }

        // Fallback: co-purchase aggregation (simple approximation across all orders)
        $rows = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->whereIn('oi1.product_variant_id', $purchased)
            ->whereNotIn('oi2.product_variant_id', $purchased)
            ->select('oi2.product_variant_id', DB::raw('SUM(oi2.quantity) as score'))
            ->groupBy('oi2.product_variant_id')
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        $recommendedIds = $rows->pluck('product_variant_id')->filter()->unique()->toArray();

        if (empty($recommendedIds)) {
            return collect();
        }

        // Load product variant models (with product relation) preserving order
        $variants = ProductVariant::with('product')
            ->whereIn('id', $recommendedIds)
            ->get()
            ->sortBy(function ($v) use ($recommendedIds) {
                return array_search($v->id, $recommendedIds);
            })
            ->values();

        return $variants;
    }
}
