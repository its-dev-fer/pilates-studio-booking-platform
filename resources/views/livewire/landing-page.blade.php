<div class="relative overflow-hidden bg-slate-950 text-white">
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
    </style>

    <section id="hero" class="relative flex min-h-screen items-center overflow-hidden">
        <video autoplay loop muted playsinline class="absolute inset-0 z-0 h-full w-full object-cover opacity-35">
            <source src="https://videos.pexels.com/video-files/6023202/6023202-uhd_2560_1440_25fps.mp4" type="video/mp4" />
        </video>
        <div class="absolute inset-0 z-10 bg-linear-to-b from-slate-950/60 via-slate-950/70 to-slate-950"></div>
        <div class="glow-pulse absolute -left-24 top-20 z-10 h-48 w-48 rounded-full bg-primary/30 blur-3xl"></div>
        <div class="glow-pulse absolute -right-20 bottom-24 z-10 h-56 w-56 rounded-full bg-emerald-300/20 blur-3xl"></div>

        <div class="relative z-20 mx-auto w-full max-w-7xl px-4 pb-16 pt-28 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
                <div class="space-y-6 text-center lg:text-left">
                    <p class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] text-primary">
                        PILATES REFORMER + MOVIMIENTO CONSCIENTE
                    </p>
                    <h1 class="text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">
                        Tu cuerpo cambia cuando tu energía cambia.
                    </h1>
                    <p class="mx-auto max-w-xl text-sm leading-relaxed text-slate-200 sm:text-base lg:mx-0">
                        Clases dinámicas, personalizadas y en grupos reducidos para fortalecer, estilizar y mejorar tu postura.
                        Vive la experiencia Hannah Reforme desde tu primera sesión.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-center lg:justify-start">
                        <a href="#reserva" class="inline-flex items-center justify-center rounded-full bg-primary px-7 py-3 text-sm font-bold text-black transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_0_35px_rgba(223,232,211,0.5)] sm:text-base">
                            Reservar Mi Primera Clase
                        </a>
                        <a href="/clientes/login" class="inline-flex items-center justify-center rounded-full border border-white/30 bg-white/10 px-7 py-3 text-sm font-semibold text-white backdrop-blur transition hover:border-white hover:bg-white hover:text-slate-900 sm:text-base">
                            Ya Soy Cliente
                        </a>
                    </div>
                </div>

                <div class="float-soft mx-auto w-full max-w-md rounded-3xl border border-white/15 bg-white/10 p-5 shadow-2xl backdrop-blur-md sm:p-7">
                    <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-primary">Resultados Reales</p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl border border-white/15 bg-black/20 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">+20</p>
                            <p class="mt-1 text-[11px] text-slate-300 sm:text-xs">Alumnos activos</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-black/20 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">4.9</p>
                            <p class="mt-1 text-[11px] text-slate-300 sm:text-xs">Calificación</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-black/20 p-3">
                            <p class="text-xl font-black text-primary sm:text-2xl">6</p>
                            <p class="mt-1 text-[11px] text-slate-300 sm:text-xs">Instructores</p>
                        </div>
                    </div>
                    <div class="mt-5 rounded-2xl border border-emerald-200/20 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                        "Nuestra misión es que te sientas fuerte, ligera y orgullosa de tu progreso en cada visita."
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="clases" class="relative bg-slate-950 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Nuestras Clases</h2>
                <p class="mx-auto mt-3 max-w-3xl text-3xl font-black leading-tight text-white sm:text-4xl">
                    Elige la experiencia que tu cuerpo necesita hoy
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="group rounded-3xl border border-white/10 bg-linear-to-b from-white/10 to-white/5 p-6 transition duration-300 hover:-translate-y-1 hover:border-primary/60 hover:shadow-[0_20px_60px_-35px_rgba(223,232,211,0.65)]">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-black">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Reformer Sculpt</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Clase intensa en máquina reformer para tonificar abdomen, glúteo y espalda, con secuencias fluidas y controladas.
                    </p>
                    <ul class="mt-4 space-y-2 text-xs text-slate-200">
                        <li>45 minutos de trabajo completo</li>
                        <li>Correcciones personalizadas en cada bloque</li>
                        <li>Nivel: intermedio a avanzado</li>
                    </ul>
                </article>

                <article class="group rounded-3xl border border-white/10 bg-linear-to-b from-white/10 to-white/5 p-6 transition duration-300 hover:-translate-y-1 hover:border-primary/60 hover:shadow-[0_20px_60px_-35px_rgba(223,232,211,0.65)]">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-black">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Strength Flow</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Combinamos fuerza funcional con movilidad para ganar estabilidad, prevenir lesiones y mejorar tu rendimiento diario.
                    </p>
                    <ul class="mt-4 space-y-2 text-xs text-slate-200">
                        <li>Trabajo con ligas, peso corporal y reformer</li>
                        <li>Enfoque técnico y progresivo</li>
                        <li>Nivel: todos los niveles</li>
                    </ul>
                </article>

                <article class="group rounded-3xl border border-white/10 bg-linear-to-b from-white/10 to-white/5 p-6 transition duration-300 hover:-translate-y-1 hover:border-primary/60 hover:shadow-[0_20px_60px_-35px_rgba(223,232,211,0.65)]">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-black">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Restore & Core</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-300">
                        Sesión restaurativa para liberar tensión, activar core profundo y recuperar energía a través de respiración guiada.
                    </p>
                    <ul class="mt-4 space-y-2 text-xs text-slate-200">
                        <li>Movilidad y elongación profunda</li>
                        <li>Ideal para estrés o fatiga muscular</li>
                        <li>Nivel: principiante a intermedio</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section id="contenido-clases" class="bg-slate-900 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 max-w-3xl">
                <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Contenido De Las Clases</h2>
                <p class="mt-3 text-3xl font-black leading-tight text-white sm:text-4xl">¿Qué trabajamos en cada sesión?</p>
                <p class="mt-4 text-sm leading-relaxed text-slate-300 sm:text-base">
                    Cada clase está estructurada para que sepas exactamente qué estás haciendo, por qué lo haces y cómo te ayuda a progresar semana a semana.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 sm:p-8">
                    <h3 class="text-xl font-bold text-white">Estructura Clase Reformer (50 min)</h3>
                    <div class="mt-6 space-y-4">
                        <div class="rounded-2xl border border-primary/30 bg-primary/10 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">01. Activación</p>
                            <p class="mt-2 text-sm text-slate-100">Respiración, alineación y activación de core para preparar el cuerpo.</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-black/25 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">02. Bloque Central</p>
                            <p class="mt-2 text-sm text-slate-100">Trabajo por zonas: tren inferior, tren superior y estabilidad lumbar.</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-black/25 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">03. Integración</p>
                            <p class="mt-2 text-sm text-slate-100">Secuencias funcionales para coordinación, equilibrio y control corporal.</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-black/25 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary">04. Cierre</p>
                            <p class="mt-2 text-sm text-slate-100">Movilidad, estiramiento y recuperación activa para mejorar postura.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-linear-to-b from-emerald-200/10 to-primary/10 p-6 sm:p-8">
                    <h3 class="text-xl font-bold text-white">Lo Que Vas A Lograr</h3>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/20 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-primary">Fuerza Profunda</p>
                            <p class="mt-2 text-xs text-slate-200">Activas músculos estabilizadores que normalmente no trabajas.</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-primary">Postura Elegante</p>
                            <p class="mt-2 text-xs text-slate-200">Mejoras tu alineación para verte y sentirte mejor todo el día.</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-primary">Movilidad Real</p>
                            <p class="mt-2 text-xs text-slate-200">Ganas rango de movimiento sin dolor y con control.</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-black/20 p-4">
                            <p class="text-sm font-semibold text-primary">Energía Sostenible</p>
                            <p class="mt-2 text-xs text-slate-200">Entrenas sin agotarte de más y recuperas mejor entre sesiones.</p>
                        </div>
                    </div>
                    <a href="#reserva" class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 text-sm font-bold text-black transition hover:-translate-y-0.5">
                        Quiero Empezar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="owner-message" class="bg-slate-950 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-stretch gap-6 lg:grid-cols-5">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8 lg:col-span-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">Mensaje De Nuestra Fundadora</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight text-white sm:text-4xl">Hola, soy Hannah</h2>
                    <p class="mt-5 text-sm leading-relaxed text-slate-200 sm:text-base">
                        "Creé este estudio para que cada persona descubra su fuerza de una forma consciente, elegante y sostenible.
                        No buscamos perfección, buscamos progreso real. En cada clase mi equipo y yo cuidamos tu técnica, tu ritmo
                        y tu confianza para que entrenar se convierta en tu momento favorito del día."
                    </p>
                    <p class="mt-4 text-sm font-semibold text-primary">- Hannah, Owner & Head Coach</p>
                </div>
                <div class="rounded-3xl border border-primary/30 bg-linear-to-b from-primary/20 to-transparent p-8 lg:col-span-2">
                    <h3 class="text-lg font-bold text-white">Nuestra Promesa</h3>
                    <ul class="mt-5 space-y-3 text-sm text-slate-200">
                        <li>Atención cercana en grupos pequeños.</li>
                        <li>Progresiones claras para todos los niveles.</li>
                        <li>Ambiente cálido, femenino y motivador.</li>
                        <li>Resultados visibles con entrenamiento inteligente.</li>
                    </ul>
                    <a href="#reserva" class="mt-8 inline-flex rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-slate-900">
                        Agenda Tu Clase
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="reserva" class="relative bg-linear-to-b from-slate-900 to-slate-950 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center sm:mb-12">
                <h3 class="text-3xl font-black text-white sm:text-4xl">Reserva tu espacio</h3>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-slate-300 sm:text-base">Completa el formulario para asegurar tu lugar. Si eres nuevo, crearemos tu cuenta al instante.</p>
            </div>

            @if (session()->has('message'))
                <div class="mb-6 rounded-xl border-l-4 border-blue-400 bg-blue-900/40 p-4 text-sm text-blue-200" role="alert">
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="rounded-3xl border border-white/15 bg-white/95 p-4 shadow-[0_30px_90px_-35px_rgba(0,0,0,0.7)] backdrop-blur sm:p-8">
                <form wire:submit.prevent="bookAppointment" class="space-y-5 sm:space-y-6">

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <label class="mb-3 block text-sm font-bold text-slate-800">Selecciona tu sucursal</label>
                        <div class="flex flex-wrap gap-2 sm:gap-3">
                            @foreach($tenants as $tenant)
                                <button type="button"
                                        wire:click="selectTenant({{ $tenant->id }})"
                                        class="rounded-full border-2 px-4 py-2 text-sm font-semibold transition-all duration-200 sm:px-5
                                        {{ $selectedTenant === $tenant->id ? 'border-primary bg-primary text-black shadow-md' : 'border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-slate-900' }}">
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
                                                ? 'border-black bg-black text-white shadow-lg'
                                                : 'border-' . $slot['color'] . '-200 bg-' . $slot['color'] . '-50 text-gray-800 hover:border-' . $slot['color'] . '-500'
                                            }}"
                                        >
                                            <span class="text-lg font-bold">{{ $slot['formatted'] }}</span>
                                            <span class="text-xs font-semibold {{ $selectedSlot === $slot['time'] ? 'text-gray-200' : 'text-' . $slot['color'] . '-600' }}">
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
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
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
                        <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-primary px-8 py-4 text-base font-bold text-black shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_18px_35px_-18px_rgba(0,0,0,0.55)]">
                            <span wire:loading.remove wire:target="bookAppointment" class="flex items-center">
                                Reservar
                                <svg class="ml-2 h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            <span wire:loading wire:target="bookAppointment" class="flex items-center">
                                <svg class="-ml-1 mr-3 h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Procesando reserva...
                            </span>
                        </button>
                        <p class="mt-3 text-center text-xs text-slate-500">Al agendar, aceptas nuestros términos y condiciones.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section id="testimonios" class="bg-primary py-16 text-slate-900 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center sm:mb-16">
                <h2 class="text-3xl font-black sm:text-4xl">Lo que dice nuestra comunidad</h2>
                <p class="mx-auto mt-4 max-w-3xl text-sm sm:text-base">Mujeres y hombres que ya transformaron su forma de moverse, entrenar y vivir su día a día.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-3xl bg-white p-7 shadow-lg transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-slate-600">"El sistema de reservas es fácil y las clases son espectaculares. En tres meses me siento más fuerte y con mejor postura."</p>
                    <p class="text-sm font-bold">Mariana L.</p>
                    <p class="text-xs text-slate-500">Cliente activa</p>
                </div>
                <div class="rounded-3xl bg-white p-7 shadow-lg transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-slate-600">"Amo poder elegir sucursal según mi día. El nivel de atención de los coaches es increíble."</p>
                    <p class="text-sm font-bold">Carlos R.</p>
                    <p class="text-xs text-slate-500">Cliente activo</p>
                </div>
                <div class="rounded-3xl bg-white p-7 shadow-lg transition hover:-translate-y-1">
                    <p class="mb-5 text-sm italic text-slate-600">"Todo es automático y práctico. Reservo en minutos y siempre salgo renovada de cada sesión."</p>
                    <p class="text-sm font-bold">Sofía M.</p>
                    <p class="text-xs text-slate-500">Cliente activa</p>
                </div>
            </div>
        </div>
    </section>

    <section id="ubicacion" class="border-t border-white/10 bg-slate-950 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <h2 class="text-3xl font-black text-white sm:text-4xl">Encuéntranos</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300 sm:text-base">Contamos con sucursales estratégicamente ubicadas para tu comodidad. Ven a conocernos y empieza tu entrenamiento hoy mismo.</p>

                    <div class="mt-8 space-y-5">
                        @foreach($tenants as $tenant)
                        <div class="flex items-start rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary text-black">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-white sm:text-lg">{{ $tenant->name }}</h3>
                                <p class="mt-1 text-sm text-slate-300">{{ $tenant->address ?? 'Dirección por definir' }}</p>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-6 flex items-center rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary text-black">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-base font-semibold text-white sm:text-lg">Contacto General</h3>
                                <p class="mt-1 text-sm text-slate-300">citas@hannareforme.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-80 overflow-hidden rounded-3xl border border-white/10 shadow-2xl sm:h-96 lg:h-auto">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3762.6617937409257!2d-99.1718817850934!3d19.42702448688785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff35f5bd1563%3A0x6c366f0e2de02ff7!2sEl%20Ángel%20de%20la%20Independencia!5e0!3m2!1ses!2smx!4v1655000000000!5m2!1ses!2smx"
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

    <footer class="bg-black py-10 sm:py-12">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <img src="{{ asset('assets/hannah_logo.png') }}" alt="Hannah Reforme Studio" class="mx-auto block h-auto w-24 invert-100">
            <p class="mt-3 text-xs text-gray-400 sm:text-sm">© {{ date('Y') }} Hannah Reforme Studio. Todos los derechos reservados.</p>
            <a href="/dashboard/login" class="mt-3 inline-block text-sm font-medium text-white transition hover:text-primary">Portal de empleados</a>
        </div>
    </footer>
</div>
