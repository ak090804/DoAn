<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;
use App\Models\KhuyenMai;

class ClientHomeController extends Controller
{
    protected $productVariantService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
    }

    public function home()
    {
        $topProducts = $this->productVariantService->getTopSellingProducts();
        $newestProducts = $this->productVariantService->getNewestProducts();
        $promotions = KhuyenMai::active()
            ->whereIn('loai', ['tang_sp', 'giam_gia_sp'])
            ->where('is_private', false)
            ->get();

        // Index gift promotions by product_variant_id for easy lookup in view
        $giftPromotions = KhuyenMai::active()
            ->where('loai', 'tang_sp')
            ->where('is_private', false)
            ->get()
            ->keyBy(function ($p) {
                return json_decode($p->data, true)['gift_product_variant_id'] ?? null;
            });

        return view('client.home', compact('topProducts', 'newestProducts', 'promotions', 'giftPromotions')); // view trang chủ
    }
}
