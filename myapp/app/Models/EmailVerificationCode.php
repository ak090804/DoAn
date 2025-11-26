<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerificationCode extends Model
{
    use HasFactory;

    protected $table = 'email_verification_codes';

    protected $fillable = [
        'email',
        'code',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
