<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class StoreCheckoutController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $sessionId = (string) $request->get('session_id', '');
        $user = $request->user();
        $payload = session('pending_store_order_checkout');

        if (! $user || $sessionId === '' || ! is_array($payload)) {
            return redirect()->route('store.checkout')->with('error', 'No encontramos una compra pendiente para confirmar.');
        }

        try {
            $stripeSession = $user->stripe()->checkout->sessions->retrieve($sessionId);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('store.checkout')->with('error', 'No pudimos validar el pago con Stripe.');
        }

        if (($stripeSession->payment_status ?? null) !== 'paid') {
            return redirect()->route('store.checkout')->with('error', 'El pago no fue procesado.');
        }

        $existingOrder = Order::query()->where('payment_reference', $sessionId)->first();
        if ($existingOrder) {
            session()->forget('pending_store_order_checkout');
            CartService::clearCart();

            return redirect()->route('store.index')->with('success', 'Pago confirmado. Tu orden #'.$existingOrder->id.' ya fue registrada.');
        }

        $order = DB::transaction(function () use ($payload, $sessionId): Order {
            $order = Order::create([
                'tenant_id' => $payload['tenant_id'],
                'user_id' => $payload['user_id'],
                'guest_name' => $payload['guest_name'],
                'guest_email' => $payload['guest_email'],
                'guest_phone' => $payload['guest_phone'],
                'delivery_type' => $payload['delivery_type'],
                'shipping_address' => $payload['shipping_address'],
                'subtotal' => $payload['subtotal'],
                'shipping_fee' => $payload['shipping_fee'],
                'total' => $payload['total'],
                'status' => 'pagado',
                'payment_method' => 'en_linea',
                'payment_reference' => $sessionId,
            ]);

            foreach ($payload['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_title' => $item['product_title'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'variant_selected' => $item['variant_selected'] ?? null,
                ]);
            }

            return $order;
        });

        $order->load([
            'tenant',
            'user',
            'items' => fn ($query) => $query->with(['product' => fn ($q) => $q->withTrashed()]),
        ]);

        $recipient = $order->user?->email ?? $order->guest_email;
        if (filled($recipient)) {
            try {
                Mail::to($recipient)->send(new OrderConfirmationMail(
                    order: $order,
                    isGuestCheckout: $order->user_id === null,
                ));
            } catch (Throwable $e) {
                report($e);
            }
        }

        session()->forget('pending_store_order_checkout');
        CartService::clearCart();

        return redirect()->route('store.index')->with('success', '¡Pago exitoso! Tu orden #'.$order->id.' ha sido recibida.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('store.checkout')->with('error', 'El pago fue cancelado. Tu pedido no se registró.');
    }
}
