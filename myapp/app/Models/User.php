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

    // Flag to avoid recursive delete loops when models delete each other
    public static $deletingFromRelation = false;

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

    protected static function booted()
    {
        static::deleting(function (User $user) {
            // Avoid recursion if deletion was initiated from related model
            if (self::$deletingFromRelation) {
                return;
            }

            self::$deletingFromRelation = true;

            // Delete related employee (if any)
            try {
                $employee = $user->employee;
                if ($employee) {
                    $employee->delete();
                }

                // Delete related customers (if any)
                $user->customer()->get()->each(function ($customer) {
                    $customer->delete();
                });
            } finally {
                self::$deletingFromRelation = false;
            }
        });
    }
}
