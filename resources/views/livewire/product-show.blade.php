@php
    $final = fn ($p) => (float) ($p->discount_price ?? $p->price);
@endphp

<div class="relative min-h-screen overflow-hidden bg-[rgb(255,255,253)] text-stone-800">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-24 top-24 h-48 w-48 rounded-full bg-primary/12 blur-3xl"></div>
        <div class="absolute -right-20 top-1/3 h-56 w-56 rounded-full bg-tertiary/25 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-primary/8 blur-3xl"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-[rgb(255,255,253)]/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('store.index') }}" class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('assets/hannah_logo.png') }}" alt="{{ config('app.name') }}" width="48" height="48" class="h-11 w-auto shrink-0" />
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Tienda</p>
                    <p class="truncate text-sm font-bold text-stone-900">Producto</p>
                </div>
            </a>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('store.index') }}#catalogo" class="hidden rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:border-primary/40 sm:inline-flex">
                    Catálogo
                </a>
                <a href="{{ route('store.cart') }}" class="relative rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:border-primary/40">
                    Carrito
                    @if(($cartCount ?? 0) > 0)
                        <span class="ml-1.5 rounded-full bg-primary px-2 py-0.5 text-xs font-bold text-[rgb(255,255,253)]">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">
        <nav class="mb-8 flex flex-wrap items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="transition hover:text-primary">Inicio</a>
            <span class="text-stone-300" aria-hidden="true">/</span>
            <a href="{{ route('store.index') }}" class="transition hover:text-primary">Tienda</a>
            @if($product->category)
                <span class="text-stone-300" aria-hidden="true">/</span>
                <a href="{{ route('store.index') }}#category-{{ $product->category_id }}" class="transition hover:text-primary">{{ $product->category->name }}</a>
            @endif
            <span class="text-stone-300" aria-hidden="true">/</span>
            <span class="font-medium text-stone-800">{{ \Illuminate\Support\Str::limit($product->title, 48) }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
            <div class="space-y-4">
                <div class="overflow-hidden rounded-3xl border border-stone-200/90 bg-stone-100 shadow-sm">
                    @if($product->images && count($product->images) > 0)
                        <img src="{{ Storage::disk('public')->url($product->images[0]) }}" alt="{{ $product->title }}" class="aspect-[4/5] w-full object-cover sm:aspect-[3/4]" />
                    @else
                        <div class="grid aspect-[4/5] w-full place-items-center text-stone-400 sm:aspect-[3/4]">
                            <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path></svg>
                        </div>
                    @endif
                </div>
                @if($product->images && count($product->images) > 1)
                    <div class="grid grid-cols-4 gap-2 sm:grid-cols-5">
                        @foreach(array_slice($product->images, 0, 5) as $img)
                            <div class="overflow-hidden rounded-xl border border-stone-200 bg-stone-100">
                                <img src="{{ Storage::disk('public')->url($img) }}" alt="" class="aspect-square w-full object-cover" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div>
                    @if($product->category)
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">{{ $product->category->name }}</p>
                    @endif
                    <h1 class="mt-2 text-3xl font-black leading-tight text-stone-900 sm:text-4xl lg:text-5xl">{{ $product->title }}</h1>
                    @if($catalogVariants->count() > 1)
                        <p class="mt-3 rounded-2xl border border-primary/25 bg-primary/5 px-4 py-3 text-sm leading-relaxed text-stone-700">
                            <span class="font-bold text-primary">Catálogo unificado:</span>
                            este artículo está disponible en <strong>{{ $catalogVariants->count() }} sucursales</strong> con inventario propio. Elige la sucursal abajo para agregar al carrito (el pedido se gestiona desde esa sucursal).
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap items-baseline gap-3">
                    @if($product->discount_price)
                        <span class="text-lg text-stone-400 line-through">${{ number_format($product->price, 2) }}</span>
                        <span class="text-3xl font-black text-primary sm:text-4xl">${{ number_format($product->discount_price, 2) }}</span>
                    @else
                        <span class="text-3xl font-black text-stone-900 sm:text-4xl">${{ number_format($product->price, 2) }}</span>
                    @endif
                    @if($catalogVariants->count() > 1)
                        @php
                            $minP = $catalogVariants->min(fn ($p) => $final($p));
                            $maxP = $catalogVariants->max(fn ($p) => $final($p));
                        @endphp
                        @if(abs($minP - $maxP) > 0.009)
                            <span class="text-sm font-semibold text-stone-500">(entre sucursales: ${{ number_format($minP, 2) }} – ${{ number_format($maxP, 2) }})</span>
                        @endif
                    @endif
                </div>

                @if($mergedLocations->isNotEmpty())
                    <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-500">Visible / recogida en</p>
                        <p class="mt-2 text-sm text-stone-700">
                            {{ $mergedLocations->pluck('name')->join(', ') }}
                        </p>
                    </div>
                @endif

                @if($product->description)
                    <div class="prose prose-stone max-w-none prose-p:text-stone-600 prose-headings:text-stone-900">
                        {!! $product->description !!}
                    </div>
                @endif

                <div class="rounded-3xl border border-stone-200/90 bg-white/90 p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-black text-stone-900">Disponible por sucursal</h2>
                    <p class="mt-1 text-sm text-stone-600">Stock y precio pueden variar. Agrega desde la fila de la sucursal que prefieras.</p>
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full min-w-[320px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-stone-200 text-xs font-bold uppercase tracking-wider text-stone-500">
                                    <th class="pb-3 pr-4">Sucursal</th>
                                    <th class="pb-3 pr-4">Stock</th>
                                    <th class="pb-3 pr-4">Precio</th>
                                    <th class="pb-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($catalogVariants as $variant)
                                    @php
                                        $vFinal = $final($variant);
                                        $branch = $variant->tenant?->name ?? 'Sucursal';
                                    @endphp
                                    <tr class="align-middle">
                                        <td class="py-3 pr-4 font-semibold text-stone-900">{{ $branch }}</td>
                                        <td class="py-3 pr-4 text-stone-600">{{ $variant->stock }}</td>
                                        <td class="py-3 pr-4">
                                            @if($variant->discount_price)
                                                <span class="text-xs text-stone-400 line-through">${{ number_format($variant->price, 2) }}</span>
                                                <span class="ml-1 font-bold text-primary">${{ number_format($variant->discount_price, 2) }}</span>
                                            @else
                                                <span class="font-bold text-stone-900">${{ number_format($variant->price, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            @if($variant->stock > 0)
                                                <button
                                                    type="button"
                                                    wire:click="addToCart('{{ $variant->id }}')"
                                                    class="inline-flex items-center justify-center rounded-full bg-primary px-4 py-2 text-xs font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5 sm:text-sm"
                                                >
                                                    <span wire:loading.remove wire:target="addToCart('{{ $variant->id }}')">Agregar</span>
                                                    <span wire:loading wire:target="addToCart('{{ $variant->id }}')">…</span>
                                                </button>
                                            @else
                                                <span class="text-xs font-semibold text-stone-400">Agotado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="{{ route('store.index') }}#catalogo" class="inline-flex rounded-full border border-stone-300 bg-white/90 px-6 py-3 text-sm font-bold text-primary shadow-sm transition hover:border-primary hover:bg-[rgb(255,255,253)]">
                    ← Volver al catálogo
                </a>
            </div>
        </div>
    </div>
</div>
