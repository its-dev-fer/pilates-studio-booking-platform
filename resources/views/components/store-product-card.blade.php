@props([
    'product',
    'layout' => 'grid', // grid | row
    'groupItems' => null,
])

@php
    $items = $groupItems ?? collect([$product]);
    $locations = $items->flatMap(fn ($p) => $p->locations())->unique('id')->values();
    $final = fn ($p) => (float) ($p->discount_price ?? $p->price);
    $minPrice = $items->min(fn ($p) => $final($p));
    $maxPrice = $items->max(fn ($p) => $final($p));
    $showRange = $items->count() > 1 && abs($minPrice - $maxPrice) > 0.009;
    $detailUrl = route('store.product.show', $product->slug);
@endphp

@if($layout === 'grid')
    <article class="group relative overflow-hidden rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] shadow-sm transition duration-300 hover:-translate-y-1 hover:border-tertiary/50 hover:shadow-[0_20px_50px_-28px_rgba(94,107,88,0.18)]">
        <a href="{{ $detailUrl }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" tabindex="-1" aria-hidden="true">
            <div class="relative h-64 overflow-hidden bg-stone-100">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ Storage::disk('public')->url($product->images[0]) }}" alt="{{ $product->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110">
                @else
                    <div class="grid h-full w-full place-items-center text-stone-400">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                        </svg>
                    </div>
                @endif
                <div class="shine absolute inset-0 opacity-0 transition group-hover:opacity-100"></div>
                @if($product->discount_price)
                    <span class="absolute left-3 top-3 rounded-full bg-primary px-3 py-1 text-xs font-bold text-[rgb(255,255,253)] shadow-md">Oferta</span>
                @endif
            </div>
        </a>
        <div class="space-y-4 p-5">
            <div>
                <h3 class="line-clamp-2 text-lg font-bold text-stone-900">
                    <a href="{{ $detailUrl }}" class="transition hover:text-primary">{{ $product->title }}</a>
                </h3>
                @if($locations->isNotEmpty())
                    <p class="mt-2 text-xs leading-relaxed text-stone-500">
                        <span class="font-semibold text-stone-600">Sucursales:</span>
                        {{ $locations->pluck('name')->take(4)->join(', ') }}@if($locations->count() > 4)…@endif
                    </p>
                @endif
                @if($items->count() > 1)
                    <p class="mt-1 text-xs font-medium text-primary">Mismo producto en {{ $items->count() }} inventarios — ver ficha para elegir sucursal.</p>
                @endif
            </div>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    @if($showRange)
                        <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Desde</p>
                        <p class="text-2xl font-black text-primary">${{ number_format($minPrice, 2) }}</p>
                    @elseif($product->discount_price)
                        <p class="text-sm text-stone-400 line-through">${{ number_format($product->price, 2) }}</p>
                        <p class="text-2xl font-black text-primary">${{ number_format($product->discount_price, 2) }}</p>
                    @else
                        <p class="text-2xl font-black text-stone-900">${{ number_format($product->price, 2) }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2">
                    <a href="{{ $detailUrl }}" class="text-xs font-bold text-primary underline-offset-2 hover:underline">Ver ficha</a>
                    <button
                        wire:click="addToCart('{{ $product->id }}')"
                        class="relative inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5"
                        type="button"
                        title="Agregar al carrito ({{ $product->tenant?->name ?? 'sucursal principal' }})"
                    >
                        <span wire:loading.remove wire:target="addToCart('{{ $product->id }}')">Agregar</span>
                        <span wire:loading wire:target="addToCart('{{ $product->id }}')">Agregando...</span>
                    </button>
                </div>
            </div>
        </div>
    </article>
@else
    <article class="group flex flex-col overflow-hidden rounded-3xl border border-primary/25 bg-white/90 shadow-md transition duration-300 hover:border-primary/40 hover:shadow-[0_24px_60px_-28px_rgba(94,107,88,0.22)] md:flex-row md:items-stretch">
        <a href="{{ $detailUrl }}" class="relative h-56 shrink-0 overflow-hidden bg-stone-100 md:h-auto md:w-[46%] md:min-h-[240px] focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary/40">
            @if($product->images && count($product->images) > 0)
                <img src="{{ Storage::disk('public')->url($product->images[0]) }}" alt="{{ $product->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
            @else
                <div class="grid h-full w-full place-items-center text-stone-400">
                    <svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                    </svg>
                </div>
            @endif
            @if($product->discount_price)
                <span class="absolute left-3 top-3 rounded-full bg-tertiary px-3 py-1 text-xs font-bold text-stone-900 shadow-sm">Oferta</span>
            @endif
        </a>
        <div class="flex flex-1 flex-col justify-center gap-4 p-6 md:p-8">
            <div>
                <h3 class="text-xl font-black leading-snug text-stone-900 md:text-2xl">
                    <a href="{{ $detailUrl }}" class="transition hover:text-primary">{{ $product->title }}</a>
                </h3>
                @if($locations->isNotEmpty())
                    <p class="mt-2 text-sm text-stone-600">
                        <span class="font-semibold text-stone-800">Sucursales:</span>
                        {{ $locations->pluck('name')->take(6)->join(', ') }}@if($locations->count() > 6)…@endif
                    </p>
                @endif
                @if($items->count() > 1)
                    <p class="mt-2 text-sm font-medium text-primary">Disponible en {{ $items->count() }} inventarios — abre la ficha para comparar precio y stock.</p>
                @endif
            </div>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    @if($showRange)
                        <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Desde</p>
                        <p class="text-3xl font-black text-primary">${{ number_format($minPrice, 2) }}</p>
                    @elseif($product->discount_price)
                        <p class="text-sm text-stone-400 line-through">${{ number_format($product->price, 2) }}</p>
                        <p class="text-3xl font-black text-primary">${{ number_format($product->discount_price, 2) }}</p>
                    @else
                        <p class="text-3xl font-black text-stone-900">${{ number_format($product->price, 2) }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 sm:flex-row sm:items-center">
                    <a href="{{ $detailUrl }}" class="text-sm font-bold text-primary underline-offset-2 hover:underline">Ver ficha</a>
                    <button
                        wire:click="addToCart('{{ $product->id }}')"
                        class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5"
                        type="button"
                    >
                        <span wire:loading.remove wire:target="addToCart('{{ $product->id }}')">Agregar al carrito</span>
                        <span wire:loading wire:target="addToCart('{{ $product->id }}')">Agregando...</span>
                    </button>
                </div>
            </div>
        </div>
    </article>
@endif
