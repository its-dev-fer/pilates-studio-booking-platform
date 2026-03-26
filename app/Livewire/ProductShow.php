<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;

    public function mount(string $slug): void
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->product = $product->load([
            'category',
            'tenant',
            'tenants',
        ]);
    }

    /**
     * Variantes del mismo artículo en otras sucursales (misma catalog_key).
     *
     * @return Collection<int, Product>
     */
    protected function catalogVariants(): Collection
    {
        if (! $this->product->catalog_key) {
            return collect([$this->product]);
        }

        return Product::query()
            ->where('catalog_key', $this->product->catalog_key)
            ->where('is_active', true)
            ->with(['tenant', 'tenants', 'category'])
            ->orderBy('title')
            ->get();
    }

    public function addToCart(string $productId): void
    {
        $product = Product::query()->findOrFail($productId);

        CartService::addItem($product, 1);

        $this->dispatch('cart-updated');

        Notification::make()
            ->title('¡Agregado al carrito!')
            ->body($product->title.' se añadió a tu bolsa.')
            ->success()
            ->duration(3000)
            ->send();
    }

    public function render()
    {
        $catalogVariants = $this->catalogVariants();

        $mergedLocations = $catalogVariants
            ->flatMap(fn (Product $p) => $p->locations())
            ->unique('id')
            ->values();

        return view('livewire.product-show', [
            'cartCount' => CartService::getItemsCount(),
            'catalogVariants' => $catalogVariants,
            'mergedLocations' => $mergedLocations,
        ])->layout('layouts.app');
    }
}
