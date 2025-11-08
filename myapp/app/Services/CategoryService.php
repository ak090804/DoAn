<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\QueryException;

class CategoryService
{
    // Lấy danh sách category có phân trang
    public function getAllPaginated($perPage = 10)
    {
        return Category::paginate($perPage);
    }

    // Tạo category mới
    public function create(array $data)
    {
        return Category::create($data);
    }

    // Cập nhật category
    public function update(Category $category, array $data)
    {
        return $category->update($data);
    }

    // Xóa category
    public function delete(Category $category)
    {
        try {
            $category->delete();
            return ['success' => true];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa danh mục vì có sản phẩm liên quan.'
            ];
        }
    }
}
