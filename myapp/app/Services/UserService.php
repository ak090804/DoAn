<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class UserService
{
    // Lấy danh sách tất cả user, có thể tìm kiếm hoặc lọc theo role
    public function getAllPaginated($perPage = 10, $filters = [])
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query->paginate($perPage)->appends($filters);
    }

    // Lấy thông tin user theo email
    public function getByEmail($email)
    {
        return User::where('email', $email)->firstOrFail();
    }

    // Tạo mới user
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'customer';
        return User::create($data);
    }

    // Cập nhật user
    public function update(User $user, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    // Xóa user
    public function delete(User $user)
    {
        try {
            $user->delete();
            return ['success' => true];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa người dùng vì có dữ liệu liên quan.'
            ];
        }
    }
}
