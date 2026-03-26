<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasUuids, SoftDeletes; // Habilita UUID automático

    protected $fillable = [
        'tenant_id',
        'category_id',
        'title',
        'slug',
        'description',
        'brand',
        'price',
        'discount_price',
        'sku',
        'catalog_key',
        'stock',
        'images',
        'variants',
        'promo_start_date',
        'promo_end_date',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'variants' => 'array',
        'promo_start_date' => 'datetime',
        'promo_end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Sucursales donde el producto aparece en catálogo unificado (recogida / visibilidad).
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'product_tenant', 'product_id', 'tenant_id')
            ->withTimestamps();
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function locations(): Collection
    {
        $this->loadMissing(['tenant', 'tenants']);

        if ($this->tenants->isNotEmpty()) {
            return $this->tenants->unique('id')->values();
        }

        if ($this->tenant) {
            return collect([$this->tenant]);
        }

        return collect();
    }

    protected static function booted(): void
    {
        static::saved(function (Product $product): void {
            if (! $product->tenant_id) {
                return;
            }

            if (! $product->tenants()->where('tenants.id', $product->tenant_id)->exists()) {
                $product->tenants()->attach($product->tenant_id);
            }
        });
    }
}
