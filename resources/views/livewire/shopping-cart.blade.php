<div class="relative min-h-screen overflow-hidden bg-[rgb(255,255,253)] text-stone-800">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-24 top-24 h-48 w-48 rounded-full bg-primary/12 blur-3xl"></div>
        <div class="absolute -right-20 top-1/3 h-56 w-56 rounded-full bg-tertiary/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-primary/8 blur-3xl"></div>
    </div>

    {{-- Barra superior coherente con la tienda --}}
    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-[rgb(255,255,253)]/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('store.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/hannah_logo.png') }}" alt="{{ config('app.name') }}" width="48" height="48" class="h-11 w-auto" />
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Tienda</p>
                    <p class="truncate text-sm font-bold text-stone-900">Carrito de compras</p>
                </div>
            </a>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('store.index') }}" class="hidden rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm transition hover:border-primary/40 hover:text-primary sm:inline-flex">
                    Seguir comprando
                </a>
                @if($itemCount > 0)
                    <span class="rounded-full bg-primary px-3 py-1.5 text-xs font-bold text-[rgb(255,255,253)]">{{ $itemCount }} {{ $itemCount === 1 ? 'artículo' : 'artículos' }}</span>
                @endif
            </div>
        </div>
    </header>

    <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        {{-- Migas de pan (estilo marketplace) --}}
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="transition hover:text-primary">Inicio</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <a href="{{ route('store.index') }}" class="transition hover:text-primary">Tienda</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <span class="font-medium text-stone-800">Carrito</span>
        </nav>

        <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-black text-stone-900 sm:text-4xl">Tu carrito</h1>
                <p class="mt-1 text-sm text-stone-600 sm:text-base">
                    Revisa cantidades y precios antes de continuar. El envío lo confirmamos en el siguiente paso.
                </p>
            </div>
            @if($items->count() > 0)
                <a href="{{ route('store.index') }}" class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white/90 px-5 py-2.5 text-sm font-semibold text-primary shadow-sm transition hover:bg-[rgb(255,255,253)] sm:hidden">
                    Seguir comprando
                </a>
            @endif
        </div>

        @if($items->count() > 0)
            <div class="lg:grid lg:grid-cols-12 lg:items-start lg:gap-10">
                {{-- Lista de productos --}}
                <div class="lg:col-span-8">
                    <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white/90 shadow-sm">
                        <div class="flex items-center justify-between border-b border-stone-200/80 bg-stone-50/80 px-4 py-4 sm:px-6">
                            <h2 class="text-sm font-bold text-stone-900 sm:text-base">
                                Artículos <span class="font-normal text-stone-500">({{ $itemCount }})</span>
                            </h2>
                            <span class="hidden text-xs text-stone-500 sm:inline">Precios en MXN</span>
                        </div>

                        <ul class="divide-y divide-stone-200/80" role="list">
                            @foreach($items as $item)
                                @php
                                    $product = $item->product;
                                    $unit = $product->discount_price ?? $product->price;
                                    $lineTotal = $unit * $item->quantity;
                                    $imgs = $product->images;
                                    $img = is_array($imgs) && count($imgs) > 0 ? $imgs[0] : null;
                                    $atMax = $item->quantity >= $product->stock;
                                @endphp
                                <li class="p-4 sm:p-6" wire:key="cart-item-{{ $item->id }}">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                        <a href="{{ route('store.index') }}#catalogo" class="mx-auto shrink-0 overflow-hidden rounded-2xl border border-stone-200 bg-stone-100 sm:mx-0">
                                            <div class="h-28 w-28 sm:h-32 sm:w-32">
                                                @if($img)
                                                    <img src="{{ Storage::disk('public')->url($img) }}" alt="" class="h-full w-full object-cover transition hover:opacity-95" />
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-stone-400">
                                                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>

                                        <div class="min-w-0 flex-1 text-center sm:text-left">
                                            <h3 class="text-base font-bold leading-snug text-stone-900 sm:text-lg">
                                                <a href="{{ route('store.index') }}#catalogo" class="transition hover:text-primary">{{ $product->title }}</a>
                                            </h3>
                                            @if($product->sku)
                                                <p class="mt-1 text-xs text-stone-500">SKU: {{ $product->sku }}</p>
                                            @endif
                                            @if($item->variant_selected)
                                                <div class="mt-2 space-y-1">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Variaciones</p>
                                                    @foreach($item->variant_selected as $variantName => $variantValue)
                                                        <p class="text-xs text-stone-600">{{ $variantName }}: <span class="font-medium text-stone-800">{{ $variantValue }}</span></p>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 sm:justify-start">
                                                <button
                                                    type="button"
                                                    wire:click="removeItem({{ $item->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="text-sm font-semibold text-stone-500 underline-offset-2 transition hover:text-rose-600 hover:underline disabled:opacity-50"
                                                >
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-center gap-4 border-t border-stone-100 pt-4 sm:flex-row sm:border-0 sm:pt-0">
                                            {{-- Precio unitario (visible en desktop como columnas estilo ML) --}}
                                            <div class="hidden w-28 text-right sm:block">
                                                <p class="text-xs text-stone-500">Precio c/u</p>
                                                <p class="mt-0.5 font-bold text-stone-900">${{ number_format($unit, 2) }}</p>
                                                @if($product->discount_price)
                                                    <p class="text-xs text-stone-400 line-through">${{ number_format($product->price, 2) }}</p>
                                                @endif
                                            </div>

                                            {{-- Cantidad --}}
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xs font-medium text-stone-500">Cantidad</span>
                                                <div class="inline-flex items-stretch overflow-hidden rounded-xl border border-stone-300 bg-[rgb(255,255,253)] shadow-sm">
                                                    <button
                                                        type="button"
                                                        wire:click="decrementQuantity({{ $item->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-3 py-2 text-stone-600 transition hover:bg-stone-100 disabled:opacity-40"
                                                        aria-label="Reducir cantidad"
                                                    >−</button>
                                                    <span class="flex min-w-[2.5rem] items-center justify-center border-x border-stone-300 px-2 py-2 text-sm font-bold tabular-nums">{{ $item->quantity }}</span>
                                                    <button
                                                        type="button"
                                                        wire:click="incrementQuantity({{ $item->id }})"
                                                        wire:loading.attr="disabled"
                                                        @disabled($atMax)
                                                        class="px-3 py-2 text-stone-600 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                                                        aria-label="Aumentar cantidad"
                                                    >+</button>
                                                </div>
                                                @if($atMax)
                                                    <span class="text-[11px] text-amber-700">Máx. stock</span>
                                                @endif
                                            </div>

                                            {{-- Subtotal línea --}}
                                            <div class="text-center sm:w-32 sm:text-right">
                                                <p class="text-xs text-stone-500 sm:hidden">Subtotal</p>
                                                <p class="hidden text-xs text-stone-500 sm:block">Subtotal</p>
                                                <p class="text-lg font-black text-primary sm:text-xl">${{ number_format($lineTotal, 2) }}</p>
                                                <p class="mt-1 text-xs text-stone-500 sm:hidden">${{ number_format($unit, 2) }} c/u</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Confianza (misma narrativa que la tienda) --}}
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="flex items-start gap-3 rounded-2xl border border-primary/15 bg-primary/5 p-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-primary text-[rgb(255,255,253)]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-stone-900">Pago seguro</p>
                                <p class="mt-0.5 text-xs text-stone-600">Proceso protegido al confirmar tu pedido.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-stone-200 bg-white/80 p-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-tertiary/40 text-stone-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-stone-900">Envío a domicilio</p>
                                <p class="mt-0.5 text-xs text-stone-600">Costo y tiempo en checkout.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-2xl border border-stone-200 bg-white/80 p-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-stone-200 text-stone-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-stone-900">Recogida en sucursal</p>
                                <p class="mt-0.5 text-xs text-stone-600">Retira cuando te avisemos.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen fijo estilo checkout lateral --}}
                <aside class="mt-8 lg:col-span-4 lg:mt-0">
                    <div class="sticky top-24 space-y-4">
                        <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-white shadow-[0_20px_50px_-28px_rgba(94,107,88,0.15)]">
                            <div class="border-b border-stone-200/80 bg-linear-to-r from-primary/10 to-tertiary/10 px-5 py-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">Resumen</p>
                                <p class="mt-1 text-lg font-black text-stone-900">Total del pedido</p>
                            </div>
                            <div class="space-y-4 px-5 py-5">
                                <div class="flex justify-between text-sm">
                                    <span class="text-stone-600">Subtotal ({{ $itemCount }} {{ $itemCount === 1 ? 'artículo' : 'artículos' }})</span>
                                    <span class="font-bold tabular-nums text-stone-900">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-stone-600">Envío</span>
                                    <span class="text-right text-sm font-medium text-stone-500">Se calcula al pagar</span>
                                </div>
                                <div class="border-t border-dashed border-stone-200 pt-4">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <span class="text-base font-bold text-stone-900">Total estimado</span>
                                        <span class="text-2xl font-black text-primary tabular-nums">${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <p class="mt-2 text-xs leading-relaxed text-stone-500">Impuestos incluidos si aplican. El monto final puede variar según método de envío.</p>
                                </div>
                                <a
                                    href="{{ route('store.checkout') }}"
                                    class="flex w-full items-center justify-center rounded-full bg-primary py-3.5 text-center text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.4)] transition hover:-translate-y-0.5"
                                >
                                    Continuar al pago
                                </a>
                                <a href="{{ route('store.index') }}" class="block w-full text-center text-sm font-semibold text-primary underline-offset-2 hover:underline">
                                    Agregar más productos
                                </a>
                            </div>
                        </div>

                        <p class="px-1 text-center text-xs text-stone-500">
                            Al continuar aceptas revisar tu pedido y datos de entrega en el siguiente paso.
                        </p>
                    </div>
                </aside>
            </div>
        @else
            <div class="mx-auto max-w-lg rounded-3xl border border-stone-200/90 bg-white/90 px-8 py-16 text-center shadow-sm">
                <div class="mx-auto mb-6 grid h-20 w-20 place-items-center rounded-2xl bg-primary/10 text-primary">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-stone-900">Tu carrito está vacío</h2>
                <p class="mt-2 text-sm text-stone-600">Explora la tienda y agrega productos para verlos aquí, con el mismo estilo y confianza de siempre.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('store.index') }}" class="inline-flex justify-center rounded-full bg-primary px-8 py-3 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5">
                        Ir a la tienda
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex justify-center rounded-full border border-stone-300 bg-white px-8 py-3 text-sm font-semibold text-stone-700 transition hover:border-primary/40 hover:text-primary">
                        Volver al inicio
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
