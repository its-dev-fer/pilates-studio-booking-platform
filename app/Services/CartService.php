<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Obtiene el carrito actual (de usuario registrado o invitado)
     */
    public static function getCart(): Cart
    {
        $user = auth()->user();
        $sessionId = Session::getId();

        if ($user) {
            // Si está logueado, buscamos su carrito o le creamos uno
            return Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['tenant_id' => session('tenant_id')] // Opcional si quieres ligarlo a la sucursal actual
            );
        }

        // Si es invitado, usamos el ID de su sesión de navegador
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null, 'tenant_id' => session('tenant_id')]
        );
    }

    /**
     * Agrega un producto al carrito
     */
    public static function addItem(Product $product, int $quantity = 1, ?array $variant = null): CartItem
    {
        $cart = self::getCart();

        // Buscamos si ya tiene este producto (con la misma variante) en el carrito
        $cartItem = $cart->items()->where('product_id', $product->id)
            ->when($variant, function ($query) use ($variant) {
                return $query->whereJsonContains('variant_selected', $variant);
            })
            ->first();

        if ($cartItem) {
            // Si ya lo tiene, solo sumamos la cantidad
            $cartItem->increment('quantity', $quantity);

            return $cartItem;
        }

        if ($cart->items()->doesntExist()) {
            $cart->forceFill(['tenant_id' => $product->tenant_id])->save();
        }

        // Si es nuevo, lo creamos
        return $cartItem = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'variant_selected' => $variant,
        ]);
    }

    /**
     * Calcula el Subtotal de todo el carrito
     */
    public static function getSubtotal(): float
    {
        $cart = self::getCart();
        $subtotal = 0;

        foreach ($cart->items as $item) {
            // Respetamos el precio de descuento si existe
            $price = $item->product->discount_price ?? $item->product->price;
            $subtotal += $price * $item->quantity;
        }

        return $subtotal;
    }

    /**
     * Cuenta cuántos artículos hay en total (para el globito del carrito en el menú)
     */
    public static function getItemsCount(): int
    {
        return self::getCart()->items()->sum('quantity');
    }

    /**
     * Vacía el carrito (Útil después de pagar)
     */
    public static function clearCart(): void
    {
        $cart = self::getCart();
        $cart->items()->delete();
    }
}
