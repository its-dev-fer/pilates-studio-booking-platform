<div
    x-data="{ mobileMenu: false }"
    class="relative min-h-screen overflow-hidden bg-[rgb(255,255,253)] text-stone-800"
>
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(94, 107, 88, 0.45); }
            50% { box-shadow: 0 0 0 14px rgba(94, 107, 88, 0); }
        }
        @keyframes shine {
            0% { transform: translateX(-120%) skewX(-15deg); }
            100% { transform: translateX(260%) skewX(-15deg); }
        }
        .float-slow { animation: floatSlow 6s ease-in-out infinite; }
        .pulse-glow { animation: pulseGlow 2.2s ease-in-out infinite; }
        .shine::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 35%, rgba(255, 255, 253, 0.55) 50%, transparent 65%);
            animation: shine 4.5s linear infinite;
            pointer-events: none;
        }
    </style>

    <div class="pointer-events-none absolute inset-0">
        <div class="glow-pulse absolute -left-24 top-24 h-48 w-48 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="glow-pulse absolute -right-20 bottom-32 h-56 w-56 rounded-full bg-tertiary/30 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-primary/8 blur-3xl"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-stone-200/80 bg-[rgb(255,255,253)]/82 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/hannah_logo.png') }}" alt="{{ config('app.name') }}" width="48" height="48" class="h-12 w-auto" />
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Tienda</p>
                    <p class="text-sm font-bold text-stone-900">Studio Store</p>
                </div>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                <a href="#hero" class="rounded-full px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-primary">Destacado</a>
                <a href="#catalogo" class="rounded-full px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-primary">Catálogo</a>
                @foreach($categories as $wrap)
                    <a href="#category-{{ $wrap->category->id }}" class="rounded-full px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-primary">
                        {{ $wrap->category->name }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('store.cart') }}" class="relative rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-primary shadow-sm transition hover:border-primary/40 hover:bg-[rgb(255,255,253)]">
                    Carrito
                    @if(($cartCount ?? 0) > 0)
                        <span class="ml-2 rounded-full bg-primary px-2 py-0.5 text-xs font-bold text-[rgb(255,255,253)]">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <a href="{{ auth()->user()->hasRole('cliente') ? url('/clientes') : url('/dashboard') }}" class="rounded-full bg-primary px-4 py-2 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5">Mi cuenta</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-bold text-primary shadow-sm transition hover:border-primary hover:bg-primary hover:text-[rgb(255,255,253)]">Iniciar sesión</a>
                @endauth
            </div>

            <button @click="mobileMenu = !mobileMenu" class="grid h-10 w-10 place-items-center rounded-xl border border-stone-200 text-stone-700 lg:hidden" aria-label="Menú">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <div x-show="mobileMenu" x-transition class="border-t border-stone-200/80 bg-[rgb(255,255,253)]/95 px-4 py-4 backdrop-blur-md lg:hidden">
            <div class="mb-4 flex flex-wrap gap-2">
                <a href="#hero" class="rounded-full border border-stone-200 bg-white/90 px-3 py-1.5 text-sm font-medium text-stone-700">Destacado</a>
                <a href="#catalogo" class="rounded-full border border-stone-200 bg-white/90 px-3 py-1.5 text-sm font-medium text-stone-700">Catálogo</a>
                @foreach($categories as $wrap)
                    <a href="#category-{{ $wrap->category->id }}" class="rounded-full border border-stone-200 bg-white/90 px-3 py-1.5 text-sm font-medium text-stone-700">{{ $wrap->category->name }}</a>
                @endforeach
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('store.cart') }}" class="rounded-full border border-stone-300 bg-white/90 px-4 py-2 text-sm font-semibold text-primary">Carrito ({{ $cartCount ?? 0 }})</a>
                @auth
                    <a href="{{ auth()->user()->hasRole('cliente') ? url('/clientes') : url('/dashboard') }}" class="rounded-full bg-primary px-4 py-2 text-sm font-bold text-[rgb(255,255,253)]">Mi cuenta</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-primary px-4 py-2 text-sm font-bold text-[rgb(255,255,253)]">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </header>

    <section id="hero" class="scroll-mt-28 relative mx-auto grid max-w-7xl gap-10 px-4 pb-12 pt-10 sm:px-6 lg:grid-cols-2 lg:px-8">
        @if($latestProduct)
            <div class="space-y-6">
                <span class="inline-flex items-center rounded-full border border-stone-300/80 bg-white/70 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-primary">
                    RECIÉN AGREGADO
                </span>
                <h1 class="text-4xl font-black leading-tight text-stone-900 sm:text-5xl">
                    {{ $latestProduct->title }}
                </h1>
                <div class="flex flex-wrap items-baseline gap-3">
                    @if($latestProduct->discount_price)
                        <span class="text-lg text-stone-400 line-through">${{ number_format($latestProduct->price, 2) }}</span>
                        <span class="text-3xl font-black text-primary">${{ number_format($latestProduct->discount_price, 2) }}</span>
                    @else
                        <span class="text-3xl font-black text-stone-900">${{ number_format($latestProduct->price, 2) }}</span>
                    @endif
                </div>
                @if(isset($latestLocations) && $latestLocations->isNotEmpty())
                    <p class="max-w-xl text-sm text-stone-600">
                        <span class="font-semibold text-stone-800">Disponible en:</span>
                        {{ $latestLocations->pluck('name')->join(', ') }}
                    </p>
                @endif
                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        wire:click="addToCart('{{ $latestProduct->id }}')"
                        class="inline-flex items-center justify-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-[rgb(255,255,253)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_12px_40px_-12px_rgba(94,107,88,0.45)] sm:text-base"
                    >
                        <span wire:loading.remove wire:target="addToCart('{{ $latestProduct->id }}')">Agregar al carrito</span>
                        <span wire:loading wire:target="addToCart('{{ $latestProduct->id }}')">Agregando...</span>
                    </button>
                    <a href="{{ route('store.product.show', $latestProduct->slug) }}" class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white/90 px-7 py-3 text-sm font-semibold text-primary shadow-sm backdrop-blur transition hover:border-primary/40 hover:bg-[rgb(255,255,253)] sm:text-base">
                        Ver ficha
                    </a>
                    <a href="#catalogo" class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white/90 px-7 py-3 text-sm font-semibold text-primary shadow-sm backdrop-blur transition hover:border-primary/40 hover:bg-[rgb(255,255,253)] sm:text-base">
                        Ver catálogo
                    </a>
                </div>
            </div>
            <div class="float-slow relative overflow-hidden rounded-3xl border border-stone-200/90 bg-white/75 shadow-xl shadow-stone-900/5 backdrop-blur-md">
                <div class="relative aspect-[4/5] max-h-[min(520px,70vh)] w-full overflow-hidden bg-stone-100 sm:aspect-[3/4] lg:max-h-none lg:min-h-[420px]">
                    @if($latestProduct->images && count($latestProduct->images) > 0)
                        <img src="{{ Storage::disk('public')->url($latestProduct->images[0]) }}" alt="{{ $latestProduct->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="grid h-full w-full place-items-center text-stone-400">
                            <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path></svg>
                        </div>
                    @endif
                    @if($latestProduct->discount_price)
                        <span class="absolute left-4 top-4 rounded-full bg-primary px-4 py-1.5 text-xs font-bold text-[rgb(255,255,253)] shadow-md">Oferta</span>
                    @endif
                </div>
            </div>
        @else
            <div class="space-y-6">
                <span class="inline-flex items-center rounded-full border border-stone-300/80 bg-white/70 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-primary">
                    TIENDA OFICIAL
                </span>
                <h1 class="text-4xl font-black leading-tight text-stone-900 sm:text-5xl">
                    Pronto <span class="text-primary">novedades</span>
                </h1>
                <p class="max-w-xl text-base leading-relaxed text-stone-600 sm:text-lg">
                    Estamos preparando el catálogo. Vuelve en unos días o explora el resto del sitio.
                </p>
                <a href="{{ url('/') }}" class="inline-flex rounded-full bg-primary px-7 py-3 text-sm font-bold text-[rgb(255,255,253)] transition hover:-translate-y-0.5">Volver al inicio</a>
            </div>
            <div class="rounded-3xl border border-dashed border-stone-300 bg-stone-50/80 p-12 text-center text-stone-500">
                Sin productos destacados por ahora.
            </div>
        @endif
    </section>

    <section class="relative mx-auto max-w-7xl px-4 pb-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-stone-200/90 bg-white/80 p-6 shadow-sm backdrop-blur-sm sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-black text-stone-900 sm:text-3xl">Compra con tranquilidad</h2>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600 sm:text-base">
                        Proceso claro, entrega flexible y atención humana. Elige cómo recibir tu pedido.
                    </p>
                    <a href="#catalogo" class="mt-5 inline-flex rounded-full bg-primary px-6 py-3 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition hover:-translate-y-0.5">
                        Ver productos
                    </a>
                </div>
                <ul class="grid flex-1 gap-4 sm:grid-cols-3">
                    <li class="rounded-2xl border border-primary/20 bg-primary/5 p-4">
                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-[rgb(255,255,253)]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <p class="font-bold text-stone-900">Compras seguras</p>
                        <p class="mt-1 text-xs text-stone-600 sm:text-sm">Pagos y datos tratados con cuidado. Sin sorpresas al finalizar.</p>
                    </li>
                    <li class="rounded-2xl border border-stone-200 bg-[rgb(255,255,253)] p-4">
                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-tertiary/40 text-stone-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <p class="font-bold text-stone-900">Envío a domicilio</p>
                        <p class="mt-1 text-xs text-stone-600 sm:text-sm">Recibe en casa donde tengamos cobertura. Te avisamos cuando salga.</p>
                    </li>
                    <li class="rounded-2xl border border-stone-200 bg-[rgb(255,255,253)] p-4">
                        <div class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-stone-200 text-stone-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <p class="font-bold text-stone-900">Recogida en sucursal</p>
                        <p class="mt-1 text-xs text-stone-600 sm:text-sm">Pásate por el estudio y recoge sin filas eternas cuando esté listo.</p>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <main id="catalogo" class="relative mx-auto max-w-7xl space-y-16 px-4 py-16 sm:px-6 lg:space-y-24 lg:px-8">
        @forelse($categories as $index => $wrap)
            @php
                $category = $wrap->category;
            @endphp
            @if($index % 2 === 0)
                {{-- Diseño A: encabezado clásico + grid de tarjetas verticales --}}
                <section id="category-{{ $category->id }}" class="scroll-mt-28">
                    <div class="mb-8 flex flex-col gap-2 border-b border-stone-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">{{ $category->name }}</p>
                            <h2 class="mt-1 text-3xl font-black text-stone-900 sm:text-4xl">Explora la selección</h2>
                        </div>
                        <p class="max-w-md text-sm text-stone-600">{{ $wrap->catalog_groups->count() }} {{ $wrap->catalog_groups->count() === 1 ? 'producto' : 'productos' }} en vitrina</p>
                    </div>
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($wrap->catalog_groups as $group)
                            <x-store-product-card :product="$group['primary']" :group-items="$group['items']" layout="grid" />
                        @endforeach
                    </div>
                </section>
            @else
                {{-- Diseño B: bloque con acento + tarjetas horizontales --}}
                <section id="category-{{ $category->id }}" class="scroll-mt-28 rounded-3xl border border-primary/20 bg-linear-to-br from-primary/[0.06] via-[rgb(255,255,253)] to-tertiary/15 p-6 shadow-sm sm:p-10 lg:p-12">
                    <div class="mb-10 max-w-2xl">
                        <span class="inline-flex rounded-full border border-primary/30 bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-wider text-primary">Colección</span>
                        <h2 class="mt-4 text-3xl font-black text-stone-900 sm:text-4xl lg:text-5xl">{{ $category->name }}</h2>
                        <p class="mt-3 text-sm text-stone-600 sm:text-base">Piezas pensadas para tu práctica. Desliza y elige la tuya.</p>
                    </div>
                    <div class="flex flex-col gap-6 lg:gap-8">
                        @foreach($wrap->catalog_groups as $group)
                            <x-store-product-card :product="$group['primary']" :group-items="$group['items']" layout="row" />
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
            <div class="rounded-3xl border border-stone-200/90 bg-white/80 p-16 text-center shadow-sm">
                <h3 class="text-xl font-bold text-stone-900">Sin categorías con productos</h3>
                <p class="mt-2 text-stone-600">Vuelve pronto.</p>
            </div>
        @endforelse
    </main>

    <a
        href="{{ route('store.cart') }}"
        class="pulse-glow fixed bottom-6 right-6 z-50 inline-flex h-16 w-16 items-center justify-center rounded-full bg-primary text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.45)] transition hover:scale-110"
        title="Ir al carrito"
    >
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2m0 0L7 13h10l3-8H5.4zM7 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"></path>
        </svg>
        @if(($cartCount ?? 0) > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-h-6 min-w-6 items-center justify-center rounded-full bg-tertiary px-1 text-xs font-extrabold text-stone-900 ring-2 ring-[rgb(255,255,253)]">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </a>
</div>
