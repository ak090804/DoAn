<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Customer extends Model
{
    use HasFactory;

    // Table customers
    protected $fillable = [
        'name',
        'phone',
        'address', 
        'user_id', 
    ];

    // Quan hệ với user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    protected static function booted()
    {
        static::deleting(function (Customer $customer) {
            if (User::$deletingFromRelation) {
                return;
            }

            User::$deletingFromRelation = true;
            try {
                if ($customer->user_id) {
                    $user = User::find($customer->user_id);
                    if ($user) {
                        $user->delete();
                    }
                }
            } finally {
                User::$deletingFromRelation = false;
            }
        });
    }

}
