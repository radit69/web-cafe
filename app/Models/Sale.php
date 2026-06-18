<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'total',
        'items',
        'payment_method',
        'payment_status',
        'customer_name',
        'amount_paid',
        'change',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'integer',
    ];

    public static function generateCode(): string
    {
        do {
            $code = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}

