<div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto mb-8 mt-9 max-w-7xl">
        <a href="/clientes" class="inline-flex items-center text-sm font-medium text-stone-600 transition-colors hover:text-primary">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver a mi Panel
        </a>
    </div>
    <div class="mx-auto max-w-7xl">
        <div class="mb-12 text-center">
            <span class="inline-flex rounded-full border border-stone-300/80 bg-white/80 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary shadow-sm">
                Créditos y paquetes
            </span>
            <h2 class="mt-4 text-3xl font-black text-stone-900 sm:text-4xl">Adquiere tus créditos</h2>
            <p class="mx-auto mt-4 max-w-3xl text-base text-stone-600 sm:text-lg">
                @if(session()->has('pending_appointment'))
                    Para confirmar tu clase del <strong class="text-stone-800">{{ session('pending_appointment')['date'] }}</strong>, necesitas adquirir un paquete de créditos.
                @else
                    Compra un paquete para poder agendar tus próximas clases. Un crédito equivale a una sesión.
                @endif
            </p>
        </div>

        @if($activeCredits > 0)
            <div class="mx-auto mb-12 max-w-3xl rounded-2xl border border-tertiary/50 bg-tertiary/20 p-6 shadow-sm">
                <div class="flex items-start">
                    <svg class="mr-3 h-6 w-6 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold text-stone-900">Ya tienes créditos activos</h3>
                        <p class="mt-1 text-sm text-stone-700 sm:text-base">Actualmente tienes <strong class="text-primary">{{ $activeCredits }} créditos disponibles</strong>. Según nuestras políticas, solo puedes adquirir un nuevo paquete cuando tus créditos actuales se hayan agotado por completo.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($packages as $package)
            <div class="group relative overflow-hidden rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] p-6 shadow-md shadow-stone-900/5 transition duration-300 hover:-translate-y-1 hover:border-tertiary/40 hover:shadow-[0_24px_50px_-28px_rgba(94,107,88,0.15)] sm:p-8">
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-tertiary/30 blur-2xl"></div>
                <div class="relative flex h-full flex-col justify-between">
                    <div>
                        <h3 class="mb-4 text-center text-2xl font-black text-stone-900">{{ $package->name }}</h3>
                        <div class="mb-6 text-center">
                            <span class="text-5xl font-black tracking-tight text-stone-900">${{ number_format($package->price, 2) }}</span>
                            <span class="ml-1 text-base font-semibold text-stone-500">MXN</span>
                        </div>

                        <div class="mb-6 rounded-2xl border border-primary/25 bg-primary/10 p-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-600">Incluye</p>
                            <p class="mt-2 text-3xl font-black text-primary">{{ $package->credits_amount }}</p>
                            <p class="text-sm font-medium text-stone-700">créditos (clases)</p>
                        </div>

                        <ul class="mb-8 space-y-3">
                            <li class="flex items-center text-sm text-stone-600">
                                <svg class="mr-2 h-5 w-5 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                1 crédito equivale a 1 clase
                            </li>
                            <li class="flex items-center text-sm text-stone-600">
                                <svg class="mr-2 h-5 w-5 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Vigencia de 30 días
                            </li>
                            <li class="flex items-center text-sm text-stone-600">
                                <svg class="mr-2 h-5 w-5 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Compra segura y activación inmediata
                            </li>
                        </ul>
                    </div>

                    @if($activeCredits > 0)
                        <button type="button" disabled class="block w-full cursor-not-allowed rounded-xl border border-stone-200 bg-stone-100 px-6 py-3 text-center text-sm font-bold text-stone-400">
                            No disponible
                        </button>
                    @else
                        <a href="{{ route('checkout.process', $package->id) }}" class="block w-full rounded-xl bg-primary px-6 py-3 text-center text-sm font-bold text-[rgb(255,255,253)] shadow-md transition hover:-translate-y-0.5 hover:shadow-[0_14px_32px_-14px_rgba(94,107,88,0.45)]">
                            Comprar paquete
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
