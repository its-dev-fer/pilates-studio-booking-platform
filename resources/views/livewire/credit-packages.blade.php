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

        @if (session('success'))
            <div class="mx-auto mb-6 max-w-3xl rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto mb-6 max-w-3xl rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

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

        @if($hasPendingPurchaseRequest)
            <div class="mx-auto mb-12 max-w-3xl rounded-2xl border border-amber-300 bg-amber-50 p-6 shadow-sm">
                <div class="flex items-start">
                    <svg class="mr-3 h-6 w-6 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold text-amber-900">Tienes una solicitud pendiente</h3>
                        <p class="mt-1 text-sm text-amber-800 sm:text-base">Tus opciones de compra están deshabilitadas hasta que un administrador o empleado valide tu pago.</p>
                    </div>
                </div>
            </div>
        @endif

        @if($packages->isEmpty())
            <div class="mx-auto max-w-3xl rounded-2xl border border-stone-200 bg-white p-6 text-center shadow-sm">
                <h3 class="text-lg font-bold text-stone-900">No hay paquetes disponibles</h3>
                <p class="mt-2 text-sm text-stone-600">Por ahora no tienes paquetes para comprar. Si necesitas ayuda, contáctanos.</p>
            </div>
        @else
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($packages as $package)
            @php($p = $pricingByPackageId[$package->id] ?? null)
            <div class="group relative overflow-hidden rounded-3xl border border-stone-200/90 bg-[rgb(255,255,253)] p-6 shadow-md shadow-stone-900/5 transition duration-300 hover:-translate-y-1 hover:border-tertiary/40 hover:shadow-[0_24px_50px_-28px_rgba(94,107,88,0.15)] sm:p-8">
                <div class="absolute -right-10 -top-10 h-28 w-28 rounded-full bg-tertiary/30 blur-2xl"></div>
                <div class="relative flex h-full flex-col justify-between">
                    <div>
                        @if($package->is_one_time_purchase)
                            <span class="mb-4 inline-flex rounded-full border border-primary/30 bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-primary">
                                Compra única
                            </span>
                        @endif
                        @if($p && ($p['promotion'] ?? null))
                            <span class="mb-4 inline-flex rounded-full border border-amber-400/60 bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-amber-900">
                                Promoción activa
                            </span>
                        @endif
                        @if($p && ($p['applied_label'] ?? null) === 'Precio nuevo cliente')
                            <span class="mb-4 inline-flex rounded-full border border-emerald-400/60 bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-900">
                                Precio por ser cliente nuevo
                            </span>
                        @endif
                        <h3 class="mb-4 text-center text-2xl font-black text-stone-900">{{ $package->name }}</h3>
                        <div class="mb-6 text-center">
                            <x-credit-package-price-display
                                :base-price="($p ?? [])['base_price'] ?? (float) $package->price"
                                :final-price="($p ?? [])['final_price'] ?? (float) $package->price"
                                variant="card"
                            />
                            @if($p && ($p['applied_label'] ?? null))
                                <p class="mt-2 text-xs font-semibold text-stone-600">{{ $p['applied_label'] }}</p>
                            @endif
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

                    @if($hasPendingPurchaseRequest)
                        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-center text-sm font-medium text-amber-900">
                            Tienes una solicitud pendiente. No puedes realizar otra compra hasta que sea revisada.
                        </div>
                    @elseif($activeCredits > 0)
                        <button type="button" disabled class="block w-full cursor-not-allowed rounded-xl border border-stone-200 bg-stone-100 px-6 py-3 text-center text-sm font-bold text-stone-400">
                            No disponible
                        </button>
                    @else
                        <div class="space-y-3">
                            <a href="{{ route('checkout.process', $package->id) }}" class="block w-full rounded-xl bg-primary px-6 py-3 text-center text-sm font-bold text-[rgb(255,255,253)] shadow-md transition hover:-translate-y-0.5 hover:shadow-[0_14px_32px_-14px_rgba(94,107,88,0.45)]">
                                Pagar con tarjeta (Stripe)
                            </a>
                            <button
                                type="button"
                                wire:click="requestManualPurchase({{ $package->id }}, 'transfer')"
                                wire:loading.attr="disabled"
                                wire:target="requestManualPurchase"
                                class="block w-full rounded-xl border border-primary/35 bg-white px-6 py-3 text-center text-sm font-bold text-primary transition hover:bg-primary/5 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Pagar por transferencia
                            </button>
                            @if($allowCashManualPayment)
                                <button
                                    type="button"
                                    wire:click="requestManualPurchase({{ $package->id }}, 'cash')"
                                    wire:loading.attr="disabled"
                                    wire:target="requestManualPurchase"
                                    class="block w-full rounded-xl border border-stone-300 bg-white px-6 py-3 text-center text-sm font-bold text-stone-700 transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    Pagar por efectivo
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
