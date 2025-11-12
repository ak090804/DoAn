<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SavedCart extends Model
{
    use HasFactory;

    protected $table = 'saved_carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'items',
        'total_price',
        'status'
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
