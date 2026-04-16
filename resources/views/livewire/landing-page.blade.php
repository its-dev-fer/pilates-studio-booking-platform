<div class="relative overflow-hidden bg-[rgb(255,255,253)] text-stone-800">
    <style>
        @keyframes floatSoft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes glowPulse {
            0%, 100% { opacity: .4; }
            50% { opacity: .85; }
        }
        .float-soft {
            animation: floatSoft 6s ease-in-out infinite;
        }
        .glow-pulse {
            animation: glowPulse 5s ease-in-out infinite;
        }
        /* Velo desigual: lectura fuerte a la izquierda / arriba; el vídeo respira al centro-derecha */
        .hero-scrim {
            background: linear-gradient(
                115deg,
                rgba(255, 255, 253, 0.94) 0%,
                rgba(255, 255, 253, 0.78) 24%,
                rgba(255, 255, 253, 0.32) 50%,
                rgba(255, 255, 253, 0.22) 68%,
                rgba(255, 255, 253, 0.62) 100%
            );
        }
        @media (max-width: 1023px) {
            .hero-scrim {
                background: linear-gradient(
                    180deg,
                    rgba(255, 255, 253, 0.92) 0%,
                    rgba(255, 255, 253, 0.88) 22%,
                    rgba(255, 255, 253, 0.28) 48%,
                    rgba(255, 255, 253, 0.22) 62%,
                    rgba(255, 255, 253, 0.9) 100%
                );
            }
        }
    </style>

    <section id="hero" class="relative flex min-h-screen scroll-mt-24 items-center overflow-hidden sm:scroll-mt-28">
        <video autoplay loop muted playsinline class="absolute inset-0 z-0 h-full w-full object-cover max-lg:object-[20%_center] lg:object-center opacity-[0.82] contrast-[1.03] saturate-[1.05]">
            <source src="https://videos.pexels.com/video-files/6111099/6111099-uhd_2560_1440_25fps.mp4" type="video/mp4" />
        </video>
        <div class="hero-scrim pointer-events-none absolute inset-0 z-10"></div>
        <div class="glow-pulse absolute -left-24 top-20 z-10 h-48 w-48 rounded-full bg-primary/15 blur-3xl"></div>
        <div class="glow-pulse absolute -right-20 bottom-24 z-10 h-56 w-56 rounded-full bg-tertiary/30 blur-3xl"></div>

        <div class="relative z-20 mx-auto w-full max-w-7xl px-4 pb-16 pt-28 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
                <div class="space-y-6 text-center lg:text-left">
                    <p class="inline-flex items-center rounded-full border border-stone-300/80 bg-white/70 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-primary">
                        PILATES REFORMER + MOVIMIENTO CONSCIENTE
                    </p>
                    <h1 class="text-4xl font-black leading-tight text-stone-900 sm:text-5xl lg:text-6xl">
                        Tu cuerpo cambia cuando tu energía cambia.
                    </h1>
                    <p class="mx-auto max-w-xl text-sm leading-relaxed text-stone-600 sm:text-base lg:mx-0">
                        Clases dinámicas, personalizadas y en grupos reducidos para fortalecer tu cuerpo, estilizar y mejorar tu postura.
                        Vive la experiencia Hannah Reforme desde tu primera sesión.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-center lg:justify-start">
                        <a href="#reserva" class="inline-flex items-center justify-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-[rgb(255,255,253)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_12px_40px_-12px_rgba(94,107,88,0.45)] sm:text-base">
                            Reservar Mi Primera Clase
                        </a>
                        <a href="/clientes/login" class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white/90 px-7 py-3 text-sm font-semibold text-primary shadow-sm backdrop-blur transition hover:border-primary/40 hover:bg-[rgb(255,255,253)] sm:text-base">
                            Ya Soy Cliente
                        </a>
                    </div>
                    <p class="text-center text-sm text-stone-600 lg:text-left">
                        <a href="#tienda" class="font-semibold text-primary underline decoration-primary/30 underline-offset-4 transition hover:decoration-primary">
                            Ver productos y categorías en la tienda en línea →
                        </a>
                    </p>
                </div>

                <div class="float-soft mx-auto w-full max-w-md rounded-3xl border border-stone-200/90 bg-white/75 p-5 shadow-xl shadow-stone-900/5 backdrop-blur-md sm:p-7">
                    <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-primary">Resultados Reales</p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl border border-stone-200 bg-[rgb(255,255,253)]/90 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">+70</p>
                            <p class="mt-1 text-[11px] text-stone-500 sm:text-xs">Alumnos activos</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-[rgb(255,255,253)]/90 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">5</p>
                            <p class="mt-1 text-[11px] text-stone-500 sm:text-xs">Calificación</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-[rgb(255,255,253)]/90 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">3</p>
                            <p class="mt-1 text-[11px] text-stone-500 sm:text-xs">Instructores</p>
                        </div>
                    </div>
                    <div class="mt-5 rounded-2xl border border-tertiary/40 bg-tertiary/20 p-4 text-sm text-stone-700">
                        "Nuestra misión es que en cada visita ganes fuerza, equilibrio y orgullo por tu progreso."
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($activePromotions->isNotEmpty())
        <section id="promociones" class="border-t border-stone-200/80 bg-linear-to-b from-amber-50 to-[rgb(255,255,253)] py-12 sm:py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Promociones activas</p>
                    <h2 class="mt-2 text-2xl font-black text-stone-900 sm:text-3xl">Aprovecha estos paquetes en oferta</h2>
                    <p class="mx-auto mt-3 max-w-2xl text-sm text-stone-600 sm:text-base">
                        Estas promociones se aplican automáticamente al pagar en línea o al solicitar compra por transferencia/efectivo.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($activePromotions as $promo)
                        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
                            <div class="mb-3 inline-flex rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">
                                Oferta vigente
                            </div>

                            <h3 class="text-xl font-black text-stone-900">{{ $promo['package_name'] }}</h3>
                            <p class="mt-1 text-sm text-stone-600">{{ $promo['credits_amount'] }} créditos</p>

                            <div class="mt-4">
                                <x-credit-package-price-display
                                    :base-price="$promo['base_price']"
                                    :final-price="$promo['final_price']"
                                    variant="landing"
                                />
                            </div>

                            <p class="mt-3 text-xs font-medium text-stone-700">
                                @if($promo['type'] === 'percent')
                                    Descuento del {{ rtrim(rtrim(number_format((float) $promo['discount_percent'], 2), '0'), '.') }}%
                                @else
                                    Precio fijo promocional
                                @endif
                            </p>

                            <p class="mt-1 text-xs text-stone-500">
                                Vigente hasta {{ $promo['ends_at']->format('d/m/Y H:i') }}
                            </p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 text-center">
                    <a href="/paquetes-disponibles" class="inline-flex items-center justify-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-[rgb(255,255,253)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_12px_40px_-12px_rgba(94,107,88,0.45)]">
                        Ver paquetes y comprar
                    </a>
                </div>
            </div>
        </section>
    @endif

    <section id="clases" class="relative border-t border-stone-200/80 bg-stone-100/60 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Pilates Reformer</h2>
                <p class="mx-auto mt-3 max-w-3xl text-3xl font-black leading-tight text-stone-900 sm:text-4xl">
                    Experiencias en máquina reformer
                </p>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-stone-600 sm:text-base">
                    Todas nuestras clases son Pilates Reformer: mismo método y máquina, distintos ritmos e intensidades según lo que necesites.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3 items-center justify-center">
                <article class="group rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-tertiary/50 hover:shadow-[0_20px_50px_-28px_rgba(94,107,88,0.18)]">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-[rgb(255,255,253)]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-stone-900">Reformer</h3>
                    <p class="mt-3 text-sm leading-relaxed text-stone-600">
                        Clase en reformer con énfasis en técnica pilates, transiciones fluidas entre ejercicios y control del carro y los muelles.
                    <br>
                    </p>
                    <ul class="mt-4 space-y-2 text-xs text-stone-600">
                    <li>Correcciones personalizadas en cada bloque</li>
                    <li>Nivel: principiante, intermedio y avanzado</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section id="tienda" class="scroll-mt-24 border-t border-stone-200/80 bg-linear-to-b from-[rgb(255,255,253)] via-tertiary/20 to-stone-100/50 py-16 sm:scroll-mt-28 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-6 sm:mb-14 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl text-center sm:text-left">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Tienda en línea</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight text-stone-900 sm:text-4xl">
                        Lleva contigo lo que amas del estudio
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-stone-600 sm:text-base">
                        Equipo, indumentaria y complementos seleccionados para entrenar con la misma energía dentro y fuera de clase. Explora por categoría y compra con envío o recogida en sucursal.
                    </p>
                </div>
                <a
                    href="{{ route('store.index') }}"
                    class="inline-flex shrink-0 items-center justify-center self-center rounded-full border-2 border-primary bg-primary px-6 py-3 text-sm font-bold text-[rgb(255,255,253)] shadow-[0_14px_40px_-18px_rgba(94,107,88,0.45)] transition hover:-translate-y-0.5 hover:bg-primary/95 sm:self-end"
                >
                    Ir a la tienda completa
                </a>
            </div>

            @if ($storeCategories->isEmpty())
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white/70 p-10 text-center shadow-inner">
                    <p class="text-sm font-medium text-stone-700">Muy pronto publicaremos novedades en la tienda.</p>
                    <a href="{{ route('store.index') }}" class="mt-4 inline-flex text-sm font-semibold text-primary hover:underline">Visitar tienda</a>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($storeCategories as $category)
                        <a
                            href="{{ route('store.index') }}#category-{{ $category->id }}"
                            class="group relative aspect-[4/5] overflow-hidden rounded-2xl border border-stone-200/90 bg-stone-900/5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-primary/35 hover:shadow-[0_22px_50px_-28px_rgba(94,107,88,0.35)]"
                        >
                            @if (! empty($category->photo))
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->photo) }}"
                                    alt=""
                                    class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    loading="lazy"
                                    decoding="async"
                                />
                            @else
                                <div class="absolute inset-0 bg-linear-to-br from-primary/35 via-stone-200/40 to-tertiary/40"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-90">
                                    <svg class="h-14 w-14 text-primary/80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-stone-950/90 via-stone-950/55 to-transparent pt-16 pb-4 px-4">
                                <p class="text-sm font-bold text-[rgb(255,255,253)] sm:text-base">{{ $category->name }}</p>
                                <p class="mt-0.5 text-xs font-medium text-white/80">
                                    {{ $category->active_products_count }} {{ $category->active_products_count === 1 ? 'producto' : 'productos' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <p class="mt-8 text-center text-xs text-stone-500 sm:text-sm">
                    Al hacer clic irás a la tienda y te llevamos directo a esa categoría.
                </p>
            @endif
        </div>
    </section>

    <section id="contenido-clases" class="border-t border-stone-200/80 bg-[rgb(255,255,253)] py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-3xl">
                <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Contenido De Las Clases</h2>
                <p class="mt-3 text-3xl font-black leading-tight text-stone-900 sm:text-4xl">¿Qué trabajamos en cada sesión?</p>
                <p class="mt-4 text-sm leading-relaxed text-stone-600 sm:text-base">
                    En Pilates Reformer, cada sesión sigue una estructura clara sobre la máquina para que entiendas la técnica, el uso de los muelles y tu progreso semana a semana.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-stone-200/90 bg-white/80 p-6 shadow-sm sm:p-8">
                    <h3 class="text-xl font-bold text-stone-900">Estructura Clase Reformer</h3>
                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl border border-primary/25 bg-primary/10 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">01. Activación</p>
                            <p class="mt-2 text-sm text-stone-700">Respiración, alineación y activación de core para preparar el cuerpo.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">02. Bloque Central</p>
                            <p class="mt-2 text-sm text-stone-700">Trabajo por zonas: tren inferior, tren superior y estabilidad lumbar.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">03. Integración</p>
                            <p class="mt-2 text-sm text-stone-700">Secuencias en reformer para coordinación, equilibrio y control corporal.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">04. Cierre</p>
                            <p class="mt-2 text-sm text-stone-700">Meditación y relajación.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-tertiary/35 bg-linear-to-b from-tertiary/25 to-[rgb(255,255,253)] p-6 shadow-sm sm:p-8">
                    <h3 class="text-xl font-bold text-stone-900">Lo Que Vas A Lograr</h3>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4">
                            <p class="text-sm font-semibold text-primary">Fuerza Profunda</p>
                            <p class="mt-2 text-xs text-stone-600">Activas músculos estabilizadores que normalmente no trabajas.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4">
                            <p class="text-sm font-semibold text-primary">Postura Elegante</p>
                            <p class="mt-2 text-xs text-stone-600">Mejoras tu alineación para verte y sentirte mejor todo el día.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4">
                            <p class="text-sm font-semibold text-primary">Movilidad Real</p>
                            <p class="mt-2 text-xs text-stone-600">Ganas rango de movimiento sin dolor y con control.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4">
                            <p class="text-sm font-semibold text-primary">Energía Sostenible</p>
                            <p class="mt-2 text-xs text-stone-600">Entrenas sin agotarte de más y recuperas mejor entre sesiones.</p>
                        </div>
                        <div class="rounded-2xl border border-stone-200/90 bg-white/90 p-4">
                            <p class="text-sm font-semibold text-primary">Tonificación Muscular</p>
                            <p class="mt-2 text-xs text-stone-600">Tonifica los músculos estabilizadores que normalmente no trabajas.</p>
                        </div>
                    </div>
                    <a href="#reserva" class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 text-sm font-bold text-[rgb(255,255,253)] transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-primary/20">
                        Quiero Empezar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="nosotros" class="scroll-mt-24 border-t border-stone-200/80 bg-stone-100/50 py-16 sm:scroll-mt-28 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-5">
                <div class="rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] p-8 shadow-sm lg:col-span-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Palabras de Hannah</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight text-stone-900 sm:text-4xl">Hola, soy Hannia</h2>
                    <p class="mt-5 text-sm leading-relaxed text-stone-600 sm:text-base">
                        "Creé esta línea de estudios para que cada persona descubra su fuerza de una forma consciente, elegante y sostenible.
                        No buscamos perfección, buscamos progreso real. En cada clase mi equipo y yo cuidamos tu técnica, tu ritmo
                        y tu confianza para que entrenar se convierta en tu momento favorito del día."
                    </p>
                    <p class="mt-4 text-sm font-semibold text-primary">- Hannah, Owner & Head Coach</p>
                </div>
                <div class="rounded-3xl border border-primary/20 bg-linear-to-b from-primary/12 to-tertiary/15 p-8 shadow-sm lg:col-span-2">
                    <h3 class="text-lg font-bold text-stone-900">Nuestra Promesa</h3>
                    <ul class="mt-5 space-y-3 text-sm text-stone-700">
                        <li>Atención cercana en grupos pequeños.</li>
                        <li>Progresiones claras para todos los niveles.</li>
                        <li>Ambiente cálido, acogedor e inclusivo.</li>
                        <li>Resultados visibles con entrenamiento inteligente.</li>
                    </ul>
                    <a href="#reserva" class="mt-8 inline-flex rounded-full border border-stone-400/60 bg-white/80 px-6 py-3 text-sm font-semibold text-primary transition hover:border-primary/50 hover:bg-[rgb(255,255,253)]">
                        Agenda Tu Clase
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="reserva" class="relative scroll-mt-24 border-t border-stone-200/80 bg-linear-to-b from-stone-100/70 to-[rgb(255,255,253)] py-16 sm:scroll-mt-28 sm:py-20 lg:py-24">
        <div id="horarios" class="mx-auto max-w-5xl scroll-mt-24 px-4 sm:scroll-mt-28 sm:px-6 lg:px-8">
            <div class="mb-10 text-center sm:mb-12">
                <h3 class="text-3xl font-black text-stone-900 sm:text-4xl">Horarios y reserva</h3>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-stone-600 sm:text-base">Elige sucursal, fecha y horario disponible. Completa el formulario para asegurar tu lugar; si eres nuevo, crearemos tu cuenta al instante.</p>
            </div>

            @if (session()->has('message'))
                <div class="mb-6 rounded-xl border border-stone-200 border-l-4 border-l-primary bg-white/90 p-4 text-sm text-stone-700 shadow-sm" role="alert">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] p-4 shadow-[0_28px_70px_-32px_rgba(94,107,88,0.18)] backdrop-blur sm:p-8">
                <form wire:submit.prevent="bookAppointment" class="space-y-5 sm:space-y-6">

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <label class="mb-3 block text-sm font-bold text-slate-800">Selecciona tu sucursal</label>
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            @foreach($tenants as $tenant)
                                <button type="button"
                                        wire:click="selectTenant({{ $tenant->id }})"
                                        class="rounded-full border-2 px-4 py-2 text-sm font-semibold transition-all duration-200 sm:px-5
                                        {{ $selectedTenant === $tenant->id ? 'border-primary bg-primary text-[rgb(255,255,253)] shadow-md' : 'border-stone-200 bg-white text-stone-600 hover:border-primary/40 hover:text-primary' }}">
                                    {{ $tenant->name }}
                                </button>
                            @endforeach
                        </div>
                        @error('selectedTenant') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <label class="mb-1 block text-sm font-bold text-slate-800">Selecciona la fecha</label>
                        <input type="date" wire:model.live="selectedDate" min="{{ date('Y-m-d') }}" max="{{ $maxDate }}"
                               class="mt-1 block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:bg-white focus:ring-primary sm:w-1/2">
                        @error('selectedDate') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror

                        @if(count($availableSlots) > 0)
                            <div class="mt-5 border-t border-slate-200 pt-5">
                                <label class="mb-3 block text-sm font-bold text-slate-800">Horarios Disponibles</label>
                                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @forelse($availableSlots as $slot)
                                        <button
                                            type="button"
                                            wire:click="$set('selectedSlot', '{{ $slot['time'] }}')"
                                            class="flex min-h-20 flex-col items-center justify-center rounded-xl border-2 p-3 transition duration-200
                                            {{ $selectedSlot === $slot['time']
                                                ? 'border-primary bg-primary text-[rgb(255,255,253)] shadow-lg'
                                                : 'border-' . $slot['color'] . '-200 bg-' . $slot['color'] . '-50 text-gray-800 hover:border-' . $slot['color'] . '-500'
                                            }}"
                                        >
                                            <span class="text-lg font-bold">{{ $slot['formatted'] }}</span>
                                            <span class="text-xs font-semibold {{ $selectedSlot === $slot['time'] ? 'text-white/90' : 'text-' . $slot['color'] . '-600' }}">
                                                {{ $slot['available'] }} lugares libres
                                            </span>
                                        </button>
                                    @empty
                                        <div class="col-span-2 py-4 text-center text-sm text-gray-500">
                                            No hay clases disponibles para este día.
                                        </div>
                                    @endforelse
                                </div>
                                @error('selectedSlot') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        @elseif($selectedDate && $selectedTenant)
                            <div class="mt-4 rounded-xl border border-tertiary/50 bg-tertiary/20 p-4 text-sm text-stone-700">
                                No hay horarios disponibles para esta fecha. Por favor, selecciona otro día.
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <label class="mb-4 block text-sm font-bold text-slate-800">Tus Datos</label>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <input type="text" wire:model="name" placeholder="Nombre(s)" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:bg-white focus:ring-primary">
                                @error('name') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input type="text" wire:model="last_name" placeholder="Apellidos" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:bg-white focus:ring-primary">
                                @error('last_name') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input type="email" wire:model="email" placeholder="Correo electrónico" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:bg-white focus:ring-primary">
                                @error('email') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input type="tel" wire:model="phone" placeholder="Teléfono / WhatsApp" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:bg-white focus:ring-primary">
                                @error('phone') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                        <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-primary px-8 py-4 text-base font-bold text-[rgb(255,255,253)] shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_18px_35px_-18px_rgba(94,107,88,0.35)]">
                            <span wire:loading.remove wire:target="bookAppointment" class="flex items-center">
                                Reservar
                                <svg class="ml-2 h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            <span wire:loading wire:target="bookAppointment" class="flex items-center">
                                <svg class="-ml-1 mr-3 h-5 w-5 animate-spin text-[rgb(255,255,253)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Procesando reserva...
                            </span>
                        </button>
                        <p class="mt-3 text-center text-xs text-slate-500">Al agendar, aceptas nuestros términos y condiciones.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section id="membresias" class="scroll-mt-24 border-t border-stone-200/80 bg-stone-100/40 py-16 text-stone-800 sm:scroll-mt-28 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Membresías</p>
            <h2 class="mt-3 text-3xl font-black leading-tight text-stone-900 sm:text-4xl">Paquetes y créditos</h2>
            <p class="mx-auto mt-4 text-sm leading-relaxed text-stone-600 sm:text-base">
                Ofrecemos paquetes de clases de Pilates Reformer para que entrenes según tu ritmo. En recepción te explicamos opciones y vigencia; también puedes usar tus créditos al reservar en línea.
            </p>
            <a href="/paquetes-disponibles" class="mt-8 inline-flex items-center justify-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-[rgb(255,255,253)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_12px_40px_-12px_rgba(94,107,88,0.45)]">
                Adquiere tu membresía
            </a>
        </div>
    </section>

    <section id="testimonios" class="border-t border-stone-200/80 bg-tertiary/25 py-16 text-stone-800 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="text-3xl font-black text-stone-900 sm:text-4xl">Lo que dice nuestra comunidad</h2>
                <p class="mx-auto mt-4 max-w-3xl text-sm text-stone-600 sm:text-base">Personas que ya transformaron su forma de moverse, entrenar y vivir el día a día.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-3xl border border-stone-200/80 bg-[rgb(255,255,253)] p-7 shadow-md shadow-stone-900/5 transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-stone-600">"El sistema de reservas es fácil y las clases son espectaculares. En tres meses me siento más fuerte y con mejor postura."</p>
                    <p class="text-sm font-bold text-primary">Mariana L.</p>
                    <p class="text-xs text-stone-500">Miembro activo</p>
                </div>
                <div class="rounded-3xl border border-stone-200/80 bg-[rgb(255,255,253)] p-7 shadow-md shadow-stone-900/5 transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-stone-600">"Amo poder elegir sucursal según mi día. El nivel de atención de los coaches es increíble."</p>
                    <p class="text-sm font-bold text-primary">Carlos R.</p>
                    <p class="text-xs text-stone-500">Miembro activo</p>
                </div>
                <div class="rounded-3xl border border-stone-200/80 bg-[rgb(255,255,253)] p-7 shadow-md shadow-stone-900/5 transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-stone-600">"Todo es automático y práctico. Reservo en minutos y termino cada sesión con energía renovada."</p>
                    <p class="text-sm font-bold text-primary">Sofía M.</p>
                    <p class="text-xs text-stone-500">Miembro activo</p>
                </div>
            </div>
        </div>
    </section>

    <section id="ubicaciones" class="scroll-mt-24 border-t border-stone-200/80 bg-[rgb(255,255,253)] py-16 sm:scroll-mt-28 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <h2 class="text-3xl font-black text-stone-900 sm:text-4xl">Ubicaciones</h2>
                    <p class="mt-4 text-sm leading-relaxed text-stone-600 sm:text-base">Contamos con sucursales estratégicamente ubicadas para tu comodidad. Ven a conocernos y empieza tu entrenamiento hoy mismo.</p>

                    <div class="mt-8 space-y-5">
                        @foreach($tenants as $tenant)
                        <button
                            type="button"
                            wire:click="selectLocationTenant({{ $tenant->id }})"
                            class="flex w-full items-start rounded-2xl border p-4 text-left shadow-sm transition
                            {{ $selectedLocationTenant === $tenant->id
                                ? 'border-primary bg-primary/5'
                                : 'border-stone-200/90 bg-white/80 hover:border-primary/40' }}"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[rgb(255,255,253)]
                                {{ $selectedLocationTenant === $tenant->id ? 'bg-primary' : 'bg-primary/80' }}">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-stone-900 sm:text-lg">{{ $tenant->name }}</h3>
                                <p class="mt-1 text-sm text-stone-600">{{ $tenant->address ?? 'Dirección por definir' }}</p>
                            </div>
                        </button>
                        @endforeach

                        <div id="contacto" class="scroll-mt-24 mt-6 flex items-center rounded-2xl border border-stone-200/90 bg-white/80 p-4 shadow-sm sm:scroll-mt-28">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary text-[rgb(255,255,253)]">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-stone-900 sm:text-lg">Contacto</h3>
                                <p class="mt-1 text-sm text-stone-600">citas@hannareforme.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-80 overflow-hidden rounded-3xl border border-stone-200/90 shadow-lg shadow-stone-900/10 sm:h-96 lg:h-auto">
                    <iframe
                        src="{{ $this->selectedLocationMapUrl }}"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-stone-800/20 bg-stone-900 py-10 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <img src="{{ asset('assets/hannah_logo.png') }}" alt="Hannah Reforme Studio" class="mx-auto block h-auto w-24 brightness-0 invert">
            <p class="mt-3 text-xs text-stone-400 sm:text-sm">© {{ date('Y') }} Hannah Reforme Studio. Todos los derechos reservados.</p>
            <a href="/dashboard/login" class="mt-3 inline-block text-sm font-medium text-stone-300 transition hover:text-[rgb(255,255,253)]">Portal de empleados</a>
        </div>
    </footer>
</div>
