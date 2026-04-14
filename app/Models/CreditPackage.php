<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CreditPackage extends Model
{
    protected $fillable = [
        'name',
        'credits_amount',
        'stripe_product_id',
        'stripe_price_id',
        'price',
        'has_new_customer_price',
        'new_customer_price',
        'is_one_time_purchase',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'new_customer_price' => 'decimal:2',
            'credits_amount' => 'integer',
            'has_new_customer_price' => 'boolean',
            'is_one_time_purchase' => 'boolean',
        ];
    }

    public function purchases()
    {
        return $this->hasMany(CreditPackagePurchase::class);
    }

    public function promotions()
    {
        return $this->hasMany(CreditPackagePromotion::class, 'credit_package_id');
    }

    /** Promoción vigente en la fecha indicada (por defecto ahora). */
    public function getActivePromotion(?Carbon $at = null): ?CreditPackagePromotion
    {
        if (! $this->exists) {
            return null;
        }

        $at ??= now();

        return $this->promotions()
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>=', $at)
            ->first();
    }
}
