<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Filament\Notifications\Notification;
use Livewire\Component;

class StoreFront extends Component
{
    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        // Usamos nuestro servicio para agregarlo mágicamente
        CartService::addItem($product, 1);

        // Disparamos un evento por si en el futuro quieres poner un icono de carrito con el número arriba
        $this->dispatch('cart-updated');

        // Notificación nativa de Filament flotando en la pantalla
        Notification::make()
            ->title('¡Agregado al carrito!')
            ->body($product->title.' se añadió a tu bolsa.')
            ->success()
            ->duration(3000)
            ->send();
    }

    public function render()
    {
        $activeProductQuery = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0);

        $latestProduct = (clone $activeProductQuery)
            ->with(['tenant', 'tenants'])
            ->latest()
            ->first();

        $categories = Category::query()
            ->whereHas('products', function ($query) {
                $query->where('is_active', true)->where('stock', '>', 0);
            })
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->with(['tenant', 'tenants'])
                    ->latest();
            }])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $category) => $category->products->isNotEmpty())
            ->values();

        $categories = $categories->map(function (Category $category) {
            $catalogGroups = $category->products
                ->groupBy(fn (Product $p) => $p->catalog_key ?: '__single_'.$p->getKey())
                ->map(function ($items) {
                    $items = $items->values();
                    $primary = $items->sortBy(fn (Product $p) => (float) ($p->discount_price ?? $p->price))->first();

                    return [
                        'primary' => $primary,
                        'items' => $items,
                    ];
                })
                ->values();

            return (object) [
                'category' => $category,
                'catalog_groups' => $catalogGroups,
            ];
        });

        $latestLocations = $latestProduct
            ? $latestProduct->locations()
            : collect();

        return view('livewire.store-front', [
            'latestProduct' => $latestProduct,
            'latestLocations' => $latestLocations,
            'categories' => $categories,
            'cartCount' => CartService::getItemsCount(),
        ])->layout('layouts.app');
    }
}
