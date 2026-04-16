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
    public array $selectedVariants = [];

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
        $requiredVariants = $this->variantDefinitions($product);
        $selected = [];

        foreach ($requiredVariants as $definition) {
            $name = $definition['name'];
            $value = trim((string) ($this->selectedVariants[$name] ?? ''));

            if ($value === '') {
                Notification::make()
                    ->title('Selecciona todas las variaciones')
                    ->body("Elige una opcion para '{$name}' antes de agregar al carrito.")
                    ->warning()
                    ->duration(4000)
                    ->send();

                return;
            }

            if (! in_array($value, $definition['values'], true)) {
                Notification::make()
                    ->title('Variacion invalida')
                    ->body("La opcion seleccionada para '{$name}' no es valida para este producto.")
                    ->danger()
                    ->duration(4000)
                    ->send();

                return;
            }

            $selected[$name] = $value;
        }

        CartService::addItem($product, 1, $selected !== [] ? $selected : null);

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
            'variantDefinitions' => $this->variantDefinitions($this->product),
        ])->layout('layouts.app');
    }

    /**
     * @return array<int, array{name: string, values: array<int, string>}>
     */
    protected function variantDefinitions(Product $product): array
    {
        $raw = $product->variants;

        if (! is_array($raw)) {
            return [];
        }

        $definitions = [];

        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['option_name'] ?? ''));
            $values = collect($item['option_values'] ?? [])
                ->filter(fn ($v) => is_scalar($v) && trim((string) $v) !== '')
                ->map(fn ($v) => trim((string) $v))
                ->unique()
                ->values()
                ->all();

            if ($name === '' || $values === []) {
                continue;
            }

            $definitions[] = [
                'name' => $name,
                'values' => $values,
            ];
        }

        return $definitions;
    }
}
