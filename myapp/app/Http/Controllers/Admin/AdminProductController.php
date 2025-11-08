<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;

class AdminProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // Danh sách sản phẩm
    public function index(Request $request)
    {
        $categories = Category::all();
        $filters = $request->only(['category_id', 'search', 'sort']);
        $products = $this->productService->getAllPaginated(10, $filters);

        return view('admin.products.index', compact('categories', 'products', 'filters'));
    }

    // Form thêm mới
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // Lưu sản phẩm mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
        ]);

        $this->productService->create($request->only(['name', 'category_id']));

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công.');
    }

    // Form chỉnh sửa
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Cập nhật sản phẩm
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'required|exists:categories,id',
        ]);

        $this->productService->update($product, $request->only(['name', 'category_id']));

        return redirect()->route('admin.products.index')->with('success', 'Sửa sản phẩm thành công.');
    }

    // Xóa sản phẩm
    public function destroy(Product $product)
    {
        $result = $this->productService->delete($product);

        if ($result['success']) {
            return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
        } else {
            return redirect()->route('admin.products.index')->with('error', $result['message']);
        }
    }
}
