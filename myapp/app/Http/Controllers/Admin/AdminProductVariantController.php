<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\ProductVariantService;
use Illuminate\Http\Request;

class AdminProductVariantController extends Controller
{
    protected $service;

    public function __construct(ProductVariantService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search','product_id','brand','category_id','supplier_id','sort']);
        $variants = $this->service->getAllPaginated(10, $filters);
        $products = Product::with('category')->get();
        $categories = Category::all();
        $suppliers = \App\Models\Supplier::all();

        return view('admin.productVariants.index', compact('variants', 'products', 'categories', 'filters', 'suppliers'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.productVariants.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'brand' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'attribute' => 'required|string|max:50',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        $this->service->create(array_merge($request->all(), ['image' => $request->file('image')]));

        return redirect()->route('admin.productVariants.index')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::all();
        return view('admin.productVariants.edit', compact('productVariant', 'products'));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'brand' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'attribute' => 'required|string|max:50',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $this->service->update($productVariant, $data);

        return redirect()->route('admin.productVariants.index')->with('success', 'Biến thể sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(ProductVariant $productVariant)
    {
        $this->service->delete($productVariant);

        return redirect()->route('admin.productVariants.index')->with('success', 'Xóa biến thể sản phẩm thành công.');
    }
}
