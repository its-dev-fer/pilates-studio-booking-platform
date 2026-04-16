<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'address', 'transfer_account_number', 'transfer_bank_name', 'transfer_account_holder', 'shipping_fee', 'max_appointments_per_day', 'business_hours', 'capacity_per_slot',
    ];

    protected $casts = [
        'business_hours' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tenant', 'tenant_id', 'product_id')
            ->withTimestamps();
    }
}
