<div class="relative min-h-screen overflow-hidden bg-[rgb(255,255,253)] text-stone-800">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-24 top-24 h-48 w-48 rounded-full bg-primary/12 blur-3xl"></div>
        <div class="absolute -right-20 top-1/3 h-56 w-56 rounded-full bg-tertiary/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-primary/8 blur-3xl"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-[rgb(255,255,253)]/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('store.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/hannah_logo.png') }}" alt="{{ config('app.name') }}" width="48" height="48" class="h-11 w-auto" />
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Tienda</p>
                    <p class="truncate text-sm font-bold text-stone-900">Checkout</p>
                </div>
            </a>
            <a href="{{ route('store.cart') }}" class="rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:border-primary/40">
                Volver al carrito
            </a>
        </div>
    </header>

    <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="transition hover:text-primary">Inicio</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <a href="{{ route('store.index') }}" class="transition hover:text-primary">Tienda</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <a href="{{ route('store.cart') }}" class="transition hover:text-primary">Carrito</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <span class="font-medium text-stone-800">Pago</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-3xl font-black text-stone-900 sm:text-4xl">Finalizar compra</h1>
            <p class="mt-1 text-sm text-stone-600 sm:text-base">Confirma tus datos, elige cómo recibir tu pedido y el método de pago.</p>
        </div>

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if($tenants->isEmpty())
            <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-8 text-center text-amber-900">
                <p class="font-bold">No hay sucursales disponibles</p>
                <p class="mt-2 text-sm">Configura al menos una sucursal en el panel para habilitar recogida y envíos.</p>
                <a href="{{ route('store.index') }}" class="mt-4 inline-flex rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-[rgb(255,255,253)]">Volver a la tienda</a>
            </div>
        @else
        <div class="lg:grid lg:grid-cols-12 lg:items-start lg:gap-10">
            {{-- Formulario --}}
            <div class="lg:col-span-7">
                <form id="checkout-main-form" wire:submit.prevent="placeOrder" class="space-y-8">
                    <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white/90 p-6 shadow-sm sm:p-8">
                        <h2 class="border-b border-stone-200 pb-3 text-lg font-black text-stone-900">Datos de contacto</h2>
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="checkout-name" class="block text-sm font-bold text-stone-700">Nombre completo</label>
                                <input id="checkout-name" type="text" wire:model="name" @if(auth()->check()) readonly @endif class="mt-1.5 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @if(auth()->check()) bg-stone-50 @endif" />
                                @error('name') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="checkout-email" class="block text-sm font-bold text-stone-700">Correo electrónico</label>
                                <input id="checkout-email" type="email" wire:model="email" @if(auth()->check()) readonly @endif class="mt-1.5 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @if(auth()->check()) bg-stone-50 @endif" />
                                @error('email') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="checkout-phone" class="block text-sm font-bold text-stone-700">Teléfono</label>
                                <input id="checkout-phone" type="tel" wire:model="phone" class="mt-1.5 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                @error('phone') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        @if(! auth()->check())
                            <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input type="checkbox" wire:model.live="create_account" class="mt-1 rounded border-stone-300 text-primary focus:ring-primary" />
                                    <span class="text-sm font-semibold text-stone-800">Crear cuenta para mis próximas compras</span>
                                </label>
                                @if($create_account)
                                    <div class="mt-4">
                                        <label for="checkout-password" class="block text-xs font-bold text-stone-600">Contraseña</label>
                                        <input id="checkout-password" type="password" wire:model="password" class="mt-1 w-full rounded-xl border border-stone-300 px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                                        @error('password') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white/90 p-6 shadow-sm sm:p-8">
                        <h2 class="border-b border-stone-200 pb-3 text-lg font-black text-stone-900">Entrega</h2>
                        <p class="mt-3 text-sm text-stone-600">Elige si recoges en una de nuestras sucursales o si prefieres envío a domicilio. El costo de envío depende de la sucursal que surte tu pedido.</p>

                        @error('delivery_type')
                            <p class="mt-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm text-rose-800">{{ $message }}</p>
                        @enderror

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <label class="relative flex cursor-pointer flex-col rounded-2xl border-2 p-5 transition {{ $delivery_type === 'sucursal' ? 'border-primary bg-primary/5 shadow-sm' : 'border-stone-200 bg-[rgb(255,255,253)] hover:border-stone-300' }}">
                                <input type="radio" wire:model.live="delivery_type" value="sucursal" class="sr-only" />
                                <span class="flex items-center gap-2">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary text-[rgb(255,255,253)]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </span>
                                    <span class="font-bold text-stone-900">Recoger en sucursal</span>
                                </span>
                                <span class="mt-2 text-xs text-stone-600">Sin costo de envío. Te avisamos cuando esté listo.</span>
                            </label>

                            <label class="relative flex cursor-pointer flex-col rounded-2xl border-2 p-5 transition {{ $delivery_type === 'domicilio' ? 'border-primary bg-primary/5 shadow-sm' : 'border-stone-200 bg-[rgb(255,255,253)] hover:border-stone-300' }}">
                                <input type="radio" wire:model.live="delivery_type" value="domicilio" class="sr-only" />
                                <span class="flex items-center gap-2">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-tertiary/50 text-stone-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </span>
                                    <span class="font-bold text-stone-900">Envío a domicilio</span>
                                </span>
                                <span class="mt-2 text-xs text-stone-600">
                                    @if($fulfillmentTenant)
                                        Tarifa de esta sucursal: <strong class="text-primary">${{ number_format($fulfillmentTenant->shipping_fee, 2) }}</strong>
                                    @else
                                        Tarifa según sucursal de origen del pedido.
                                    @endif
                                </span>
                            </label>
                        </div>

                        @if($delivery_type === 'sucursal')
                            <div class="mt-6">
                                <label for="pickup-tenant" class="block text-sm font-bold text-stone-700">¿En qué sucursal recogerás?</label>
                                <select id="pickup-tenant" wire:model="pickup_tenant_id" class="mt-1.5 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->name }}@if($tenant->address) — {{ \Illuminate\Support\Str::limit($tenant->address, 60) }}@endif</option>
                                    @endforeach
                                </select>
                                @error('pickup_tenant_id') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        @if($delivery_type === 'domicilio')
                            <div class="mt-6 rounded-2xl border border-primary/20 bg-primary/5 p-4 text-sm text-stone-700">
                                @if($fulfillmentTenant)
                                    <p><span class="font-bold text-stone-900">Envío gestionado por:</span> {{ $fulfillmentTenant->name }}</p>
                                    <p class="mt-1 text-xs text-stone-600">El costo aplicado (${{ number_format($fulfillmentTenant->shipping_fee, 2) }}) corresponde a la tarifa configurada para esta sucursal.</p>
                                @else
                                    <p class="text-amber-800">No pudimos asociar el pedido a una sucursal. Agrega productos desde la tienda e intenta de nuevo.</p>
                                @endif
                            </div>
                            <div class="mt-4">
                                <label for="shipping-address" class="block text-sm font-bold text-stone-700">Dirección completa de envío</label>
                                <textarea id="shipping-address" wire:model="shipping_address" rows="4" placeholder="Calle, número, colonia, ciudad, código postal, referencias…" class="mt-1.5 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"></textarea>
                                @error('shipping_address') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white/90 p-6 shadow-sm sm:p-8">
                        <h2 class="border-b border-stone-200 pb-3 text-lg font-black text-stone-900">Método de pago</h2>
                        <select wire:model="payment_method" class="mt-6 w-full rounded-xl border border-stone-300 bg-[rgb(255,255,253)] px-4 py-3 text-stone-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="efectivo">Efectivo (al recoger o contra entrega)</option>
                            <option value="transferencia">Transferencia bancaria (SPEI)</option>
                            <option value="en_linea">Pago en línea (tarjeta)</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center rounded-full bg-primary py-4 text-base font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.4)] transition hover:-translate-y-0.5 disabled:opacity-60 lg:hidden">
                        <span wire:loading.remove wire:target="placeOrder">Confirmar pedido</span>
                        <span wire:loading wire:target="placeOrder">Procesando…</span>
                    </button>
                </form>
            </div>

            {{-- Resumen lateral --}}
            <aside class="mt-10 lg:col-span-5 lg:mt-0">
                <div class="sticky top-24 space-y-4">
                    <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white shadow-[0_20px_50px_-28px_rgba(94,107,88,0.15)]">
                        <div class="border-b border-stone-200/80 bg-linear-to-r from-primary/10 to-tertiary/10 px-5 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">Resumen</p>
                            <p class="mt-1 text-lg font-black text-stone-900">Tu pedido</p>
                        </div>
                        <ul class="max-h-64 divide-y divide-stone-100 overflow-y-auto px-5 py-3" role="list">
                            @foreach($items as $line)
                                @php
                                    $p = $line->product;
                                    $unit = $p->discount_price ?? $p->price;
                                    $imgs = $p->images;
                                    $thumb = is_array($imgs) && count($imgs) > 0 ? $imgs[0] : null;
                                @endphp
                                <li class="flex gap-3 py-3">
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                                        @if($thumb)
                                            <img src="{{ Storage::disk('public')->url($thumb) }}" alt="" class="h-full w-full object-cover" />
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-sm font-semibold text-stone-900">{{ $p->title }}</p>
                                        <p class="mt-0.5 text-xs text-stone-500">Cant. {{ $line->quantity }} × ${{ number_format($unit, 2) }}</p>
                                    </div>
                                    <p class="shrink-0 text-sm font-bold text-stone-900 tabular-nums">${{ number_format($unit * $line->quantity, 2) }}</p>
                                </li>
                            @endforeach
                        </ul>
                        <div class="space-y-3 border-t border-stone-200 px-5 py-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-600">Subtotal</span>
                                <span class="font-bold tabular-nums text-stone-900">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-600">Envío</span>
                                @if($delivery_type === 'domicilio')
                                    <span class="font-bold tabular-nums text-stone-900">${{ number_format($shippingFee, 2) }}</span>
                                @else
                                    <span class="font-semibold text-primary">Gratis</span>
                                @endif
                            </div>
                            <div class="border-t border-dashed border-stone-200 pt-3">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-base font-bold text-stone-900">Total</span>
                                    <span class="text-2xl font-black text-primary tabular-nums">${{ number_format($orderTotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" form="checkout-main-form" wire:loading.attr="disabled" class="hidden w-full items-center justify-center rounded-full bg-primary py-4 text-base font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.4)] transition hover:-translate-y-0.5 disabled:opacity-60 lg:flex">
                        <span wire:loading.remove wire:target="placeOrder">Confirmar pedido</span>
                        <span wire:loading wire:target="placeOrder">Procesando…</span>
                    </button>

                    <div class="rounded-2xl border border-stone-200 bg-white/80 px-4 py-3 text-center text-xs text-stone-500">
                        Al confirmar aceptas los datos de entrega y facturación indicados.
                    </div>
                </div>
            </aside>
        </div>
        @endif
    </div>
</div>
