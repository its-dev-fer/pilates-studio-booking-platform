<?php

namespace App\Livewire;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Throwable;

class Checkout extends Component
{
    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public bool $create_account = false;

    public ?string $password = null;

    public string $delivery_type = 'sucursal';

    /** Sucursal donde recogerán (solo si delivery_type = sucursal). */
    public ?int $pickup_tenant_id = null;

    public ?string $shipping_address = null;

    public string $payment_method = 'efectivo';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
            $this->phone = auth()->user()->phone;
        }

        if (CartService::getItemsCount() === 0) {
            $this->redirect(route('store.index'));

            return;
        }

        $defaultTenant = $this->resolveFulfillmentTenant() ?? Tenant::query()->orderBy('name')->first();
        $this->pickup_tenant_id = $defaultTenant?->id;
    }

    /**
     * Tenant que surte el pedido (para costo de envío a domicilio).
     */
    public function resolveFulfillmentTenant(): ?Tenant
    {
        $cart = CartService::getCart()->load(['tenant', 'items.product.tenant']);

        if ($cart->tenant_id) {
            return $cart->tenant;
        }

        return $cart->items->first()?->product?->tenant;
    }

    protected function shippingFeeAmount(): float
    {
        if ($this->delivery_type !== 'domicilio') {
            return 0.0;
        }

        $tenant = $this->resolveFulfillmentTenant();

        return $tenant ? (float) $tenant->shipping_fee : 0.0;
    }

    public function placeOrder(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'delivery_type' => 'required|in:sucursal,domicilio',
            'pickup_tenant_id' => 'required_if:delivery_type,sucursal|nullable|exists:tenants,id',
            'shipping_address' => 'required_if:delivery_type,domicilio|nullable|string|max:2000',
            'payment_method' => 'required|in:efectivo,transferencia,en_linea',
            'password' => 'required_if:create_account,true|nullable|min:6',
        ]);

        $cart = CartService::getCart()->load(['items.product']);
        $subtotal = CartService::getSubtotal();
        $shippingFee = $this->shippingFeeAmount();
        $total = $subtotal + $shippingFee;

        $fulfillment = $this->resolveFulfillmentTenant();
        if ($this->delivery_type === 'domicilio' && ! $fulfillment) {
            $this->addError('delivery_type', 'No pudimos determinar la sucursal de origen del pedido. Vuelve a la tienda e intenta de nuevo.');

            return;
        }

        $tenantId = $this->delivery_type === 'sucursal'
            ? (int) $this->pickup_tenant_id
            : (int) $fulfillment->id;

        $userId = auth()->id();

        if (! $userId) {
            $existingUser = User::where('email', $this->email)->first();

            if ($existingUser) {
                $userId = $existingUser->id;
            } elseif ($this->create_account) {
                $nameParts = User::splitFullNameForStorage($this->name);

                $newUser = User::create([
                    'name' => $nameParts['name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $this->email,
                    'phone' => filled($this->phone) ? $this->phone : '',
                    'password' => Hash::make($this->password),
                ]);
                $newUser->assignRole('cliente');
                $userId = $newUser->id;
                auth()->login($newUser);
            }
        }

        $order = Order::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'guest_name' => ! $userId ? $this->name : null,
            'guest_email' => ! $userId ? $this->email : null,
            'guest_phone' => ! $userId ? $this->phone : null,
            'delivery_type' => $this->delivery_type,
            'shipping_address' => $this->delivery_type === 'domicilio' ? $this->shipping_address : null,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'status' => 'creado',
            'payment_method' => $this->payment_method,
        ]);

        foreach ($cart->items as $cartItem) {
            $price = $cartItem->product->discount_price ?? $cartItem->product->price;

            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_title' => $cartItem->product->title,
                'unit_price' => $price,
                'quantity' => $cartItem->quantity,
                'variant_selected' => $cartItem->variant_selected,
            ]);
        }

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

        CartService::clearCart();

        session()->flash('success', '¡Tu orden #'.$order->id.' ha sido recibida!');

        $this->redirect(route('store.index'));
    }

    public function render()
    {
        $cart = CartService::getCart();
        $items = $cart->items()->with('product')->orderBy('id')->get();
        $tenants = Tenant::query()->orderBy('name')->get();
        $fulfillmentTenant = $this->resolveFulfillmentTenant();
        $subtotal = CartService::getSubtotal();
        $shippingFee = $this->shippingFeeAmount();
        $orderTotal = $subtotal + $shippingFee;

        return view('livewire.checkout', [
            'items' => $items,
            'tenants' => $tenants,
            'fulfillmentTenant' => $fulfillmentTenant,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'orderTotal' => $orderTotal,
        ])->layout('layouts.app');
    }
}
