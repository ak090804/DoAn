<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProductVariantService;

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
        return view('client.home', compact('topProducts', 'newestProducts')); // view trang chủ
    }    
}
