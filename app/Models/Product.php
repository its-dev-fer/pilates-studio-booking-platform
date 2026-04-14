<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

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

    /**
     * HTML listo para mostrar en la tienda (contenido del RichEditor de Filament / TipTap).
     */
    public function descriptionHtml(): HtmlString
    {
        if (blank($this->description)) {
            return new HtmlString('');
        }

        try {
            $html = RichContentRenderer::make($this->description)
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsVisibility('public')
                ->toHtml();
        } catch (Throwable $e) {
            report($e);

            $html = Str::of((string) $this->description)->stripTags()->squish()->toString();

            return new HtmlString($html !== '' ? '<p>'.e($html).'</p>' : '');
        }

        return new HtmlString($html);
    }

    /**
     * Texto plano para extractos (p. ej. tarjetas / meta), a partir del mismo contenido enriquecido.
     */
    public function descriptionPlainExcerpt(int $limit = 200): string
    {
        if (blank($this->description)) {
            return '';
        }

        try {
            $text = trim(RichContentRenderer::make($this->description)->toText());
        } catch (Throwable $e) {
            report($e);
            $text = trim(strip_tags((string) $this->description));
        }

        return Str::limit($text, $limit, '…') ?: '';
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
