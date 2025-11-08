<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Table users
    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
    ];

    public function customer()
    {
        return $this->hasMany(Customer::class, 'user_id');
    }
}
