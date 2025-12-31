<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expiry_time',
        'is_used',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
        'is_used' => 'boolean',
    ];
}
