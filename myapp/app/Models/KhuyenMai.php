<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    use HasFactory;

    protected $table = 'khuyen_mais';

    protected $fillable = [
        'ma',
        'ten',
        'mo_ta',
        'loai',
        'gia_tri',
        'so_tien_giam_toi_da',
        'is_private',
        'data',
        'active',
        'ngay_bat_dau',
        'ngay_ket_thuc'
    ];

    protected $casts = [
        'data' => 'array',
        'is_private' => 'boolean',
        'active' => 'boolean',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'gia_tri' => 'decimal:2',
        'so_tien_giam_toi_da' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(function ($q) {
                $q->whereNull('ngay_bat_dau')->orWhere('ngay_bat_dau', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ngay_ket_thuc')->orWhere('ngay_ket_thuc', '>=', now());
            });
    }
}
