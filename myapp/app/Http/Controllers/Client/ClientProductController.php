<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductVariantService;
use App\Services\AprioriService;
use App\Models\Category;
use App\Models\Product;

class ClientProductController extends Controller
{
    protected $productVariantService;

    public function __construct(ProductVariantService $productVariantService)
    {
        $this->productVariantService = $productVariantService;
    }

    public function products(Request $request)
    {
        $filters = [
            'search'      => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'product_id' => $request->input('product_id'),
            'sort'        => $request->input('sort'),
            'price_min'   => $request->input('price_min'),
            'price_max'   => $request->input('price_max'),
        ];

        $productVariants = $this->productVariantService->getAllPaginated(8, $filters);
        $categories = Category::all();
        $products = Product::all();

        return view('client.products', compact('productVariants', 'products', 'categories', 'filters'));
    }

    // Hiển thị chi tiết 1 sản phẩm
    public function show($id)
    {
        $productVariant = $this->productVariantService->findById($id);

        if (!$productVariant) {
            abort(404, 'Sản phẩm không tồn tại');
        }

        $recommendedProducts = (new AprioriService())->recommendByVariant($id, 10);
        $productVariant->recommendedProducts = $recommendedProducts;

        return view('client.productDetail', compact('productVariant', 'recommendedProducts'));
    }
}
