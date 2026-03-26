<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Services\CartService;
use Filament\Notifications\Notification;
use Livewire\Component;

class ShoppingCart extends Component
{
    public function incrementQuantity($itemId)
    {
        $item = CartItem::find($itemId);
        // Validar que no supere el stock real
        if ($item && $item->quantity < $item->product->stock) {
            $item->increment('quantity');
        } else {
            Notification::make()->title('Stock máximo alcanzado')->warning()->send();
        }
    }

    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);
        Notification::make()->title('Producto eliminado')->danger()->send();
    }

    public function decrementQuantity($itemId)
    {
        $item = CartItem::find($itemId);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
        } elseif ($item && $item->quantity === 1) {
            $this->removeItem($itemId);
        }
    }

    public function render()
    {
        $cart = CartService::getCart();
        $items = $cart->items()->with('product')->orderBy('id')->get();
        $subtotal = CartService::getSubtotal();
        $itemCount = (int) $items->sum('quantity');

        return view('livewire.shopping-cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'itemCount' => $itemCount,
        ])->layout('layouts.app');
    }
}
