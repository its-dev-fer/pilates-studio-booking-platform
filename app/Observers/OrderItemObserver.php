<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\Product;

class OrderItemObserver
{
    /**
     * Se ejecuta cada vez que se agrega un producto a un pedido.
     */
    public function created(OrderItem $orderItem): void
    {
        // Buscar el producto original
        $product = Product::find($orderItem->product_id);

        if ($product) {
            // Descontar la cantidad que acaban de comprar
            $product->decrement('stock', $orderItem->quantity);
        }
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Opcional: Si cancelan la orden y borran el item, regresamos el stock.
     */
    public function deleted(OrderItem $orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product) {
            $product->increment('stock', $orderItem->quantity);
        }
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
