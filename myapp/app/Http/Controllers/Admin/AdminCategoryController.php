<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\CategoryService;

class AdminCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    // Hiển thị danh sách category
    public function index()
    {
        $categories = $this->categoryService->getAllPaginated();
        return view('admin.categories.index', compact('categories'));
    }

    // Form thêm mới
    public function create()
    {
        return view('admin.categories.create');
    }

    // Lưu category mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories',
        ]);

        $this->categoryService->create($request->all());

        return redirect()->route('admin.categories.index')
            ->with('message', 'Thêm danh mục thành công!');
    }

    // Form chỉnh sửa
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Cập nhật category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        $this->categoryService->update($category, $request->all());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    // Xóa category
    public function destroy(Category $category)
    {
        $result = $this->categoryService->delete($category);

        if ($result['success']) {
            return redirect()->route('admin.categories.index')
                ->with('success', 'Xóa danh mục thành công.');
        } else {
            return redirect()->route('admin.categories.index')
                ->with('error', $result['message']);
        }
    }
}
