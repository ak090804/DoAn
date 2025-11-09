<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Database\QueryException;

class EmployeeService
{
    // Lấy danh sách nhân viên có phân trang, filter, search, sort
    public function getAllPaginated($perPage = 10, $filters = [])
    {
        $query = Employee::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        switch ($filters['sort'] ?? null) {
            case 'id_asc': $query->orderBy('id', 'asc'); break;
            case 'id_desc': $query->orderBy('id', 'desc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'hired_asc': $query->orderBy('hired_at', 'asc'); break;
            case 'hired_desc': $query->orderBy('hired_at', 'desc'); break;
            default: $query->orderBy('id', 'asc'); break;
        }

        return $query->paginate($perPage)->appends($filters);
    }

    // Tạo nhân viên mới
    public function create(array $data)
    {
        return Employee::create($data);
    }

    // Cập nhật nhân viên
    public function update(Employee $employee, array $data)
    {
        return $employee->update($data);
    }

    // Xóa nhân viên
    public function delete(Employee $employee)
    {
        try {
            // If employee has linked user, delete the user account as well
            if ($employee->user_id) {
                $user = \App\Models\User::find($employee->user_id);
                if ($user) {
                    $user->delete();
                }
            }

            // Delete employee record
            $employee->delete();
            return ['success' => true];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa nhân viên vì có dữ liệu liên quan.'
            ];
        }
    }
}
