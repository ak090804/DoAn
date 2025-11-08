<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\QueryException;

class ProductService
{
    // Lấy danh sách sản phẩm với lọc, tìm kiếm, sắp xếp và phân trang
    public function getAllPaginated($perPage = 10, $filters = [])
    {
        $query = Product::with(['category', 'productVariants']);

        // Lọc theo category_id
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sắp xếp
        switch ($filters['sort'] ?? null) {
            case 'id_asc': $query->orderBy('id', 'asc'); break;
            case 'id_desc': $query->orderBy('id', 'desc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default: $query->orderBy('id', 'asc'); break;
        }

        return $query->paginate($perPage)->appends($filters);
    }

    // Tạo sản phẩm mới
    public function create(array $data)
    {
        return Product::create($data);
    }

    // Cập nhật sản phẩm
    public function update(Product $product, array $data)
    {
        return $product->update($data);
    }

    // Xóa sản phẩm và variants
    public function delete(Product $product)
    {
        try {
            $product->variants()->delete();
            $product->delete();
            return ['success' => true];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm vì có đơn hàng liên quan.'
            ];
        }
    }
}
