<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

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

    /**
     * If this user is linked to an employee account, return it.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
}
