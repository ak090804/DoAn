<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductVariant;

class AprioriService
{
    /**
     * Recommend products based on Apriori rules, given a variant_id.
     *
     * @param int $variantId  Variant đang xem
     * @param int $limit
     * @return \Illuminate\Support\Collection  ProductVariants gợi ý
     */
    public function recommendByVariant(int $variantId, int $limit = 3)
    {
        // 1) Lấy product_id từ variant_id
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return collect();
        }
        $productId = $variant->product_id;

        // 2) Kiểm tra bảng Apriori
        if (!Schema::hasTable('apriori_recommendations')) {
            return collect();
        }

        // 3) Lấy các product_id gợi ý từ Apriori
        $rows = DB::table('apriori_recommendations')
            ->where('product_id', $productId)
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        $recommendedProductIds = $rows->pluck('recommended_product_id')->unique()->toArray();
        if (empty($recommendedProductIds)) {
            return collect();
        }

        // 4) Lấy variant đầu tiên của mỗi product gợi ý (hoặc nhiều variant nếu muốn)
        $variants = ProductVariant::with('product')
            ->whereIn('product_id', $recommendedProductIds)
            ->get()
            ->sortBy(function ($v) use ($recommendedProductIds) {
                return array_search($v->product_id, $recommendedProductIds);
            })
            ->values();

        return $variants;
    }
}
