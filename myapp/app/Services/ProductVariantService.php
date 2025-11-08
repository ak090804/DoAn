<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantService
{
    // Lấy danh sách product variant với lọc, search, sort, paginate
    public function getAllPaginated($perPage = 10, $filters = [])
    {
        $query = ProductVariant::with(['product.category']);

        // Tìm kiếm theo brand hoặc tên product
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhereHas('product', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // Lọc theo product_id
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        // Lọc theo brand
        if (!empty($filters['brand'])) {
            $query->where('brand', 'like', "%{$filters['brand']}%");
        }

        // Lọc theo category
        if (!empty($filters['category_id'])) {
            $query->whereHas('product.category', fn($q) => $q->where('id', $filters['category_id']));
        }

        // Sắp xếp
        switch ($filters['sort'] ?? null) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'id_asc': $query->orderBy('id', 'asc'); break;
            case 'id_desc': $query->orderBy('id', 'desc'); break;
            default: $query->orderBy('id', 'asc'); break;
        }

        return $query->paginate($perPage)->appends($filters);
    }

    // Tạo product variant mới
    public function create(array $data)
    {
        $productVariant = new ProductVariant();
        $this->fillData($productVariant, $data);
        $productVariant->save();
        return $productVariant;
    }

    // Cập nhật product variant
    public function update(ProductVariant $productVariant, array $data)
    {
        $this->fillData($productVariant, $data);
        $productVariant->save();
        return $productVariant;
    }

    // Xóa product variant
    public function delete(ProductVariant $productVariant)
    {
        if ($productVariant->image && Storage::exists('public/products/' . $productVariant->image)) {
            Storage::delete('public/products/' . $productVariant->image);
        }

        $productVariant->delete();
    }

    // Hàm private dùng chung để fill dữ liệu + upload ảnh
    private function fillData(ProductVariant $variant, array $data)
    {
        $variant->product_id = $data['product_id'];
        $variant->brand = $data['brand'];
        $variant->attribute = $data['attribute'];
        $variant->description = $data['description'];
        $variant->price = $data['price'];
        $variant->quantity = $data['quantity'];

        if (!empty($data['image'])) {
            // Xóa ảnh cũ nếu có
            if ($variant->image && Storage::exists('public/products/' . $variant->image)) {
                Storage::delete('public/products/' . $variant->image);
            }
            $image = $data['image'];
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $variant->image = $imageName;
        }
    }
}
