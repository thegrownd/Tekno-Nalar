<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'unsubscribed_at'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->whereNull('unsubscribed_at');
    }
}
