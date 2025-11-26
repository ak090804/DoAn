<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;
use App\Services\RecommendationService;
use App\Models\Customer;
use App\Models\KhuyenMai;

class ClientHomeController extends Controller
{
    protected $productVariantService;
    protected $recommendationService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
        $this->recommendationService = new RecommendationService();
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

        // Personalized recommendations: if user logged in and has a Customer record,
        // compute recommendations based on their purchase history.
        $recommendedProducts = collect();
        $userId = session('user_id');
        if ($userId) {
            $customer = Customer::where('user_id', $userId)->first();
            if ($customer) {
                $recommendedProducts = $this->recommendationService->recommendForCustomer($customer->id, 10);
            }
        }

        return view('client.home', compact('topProducts', 'newestProducts', 'promotions', 'giftPromotions', 'recommendedProducts')); // view trang chủ
    }
}
