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
        'is_one_time_purchase',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'credits_amount' => 'integer',
            'is_one_time_purchase' => 'boolean',
        ];
    }

    public function purchases()
    {
        return $this->hasMany(CreditPackagePurchase::class);
    }
}
