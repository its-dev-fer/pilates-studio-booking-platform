<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    protected $fillable = [
        'name',
        'credits_amount',
        'stripe_price_id',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'credits_amount' => 'integer',
        ];
    }
}
