<div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto mb-8 mt-9 max-w-7xl">
        <a href="/" class="inline-flex items-center text-sm font-medium text-stone-600 transition-colors hover:text-primary">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver al inicio
        </a>
    </div>

    <div class="mx-auto max-w-7xl">
        <div class="mb-8 text-center">
            <span class="inline-flex rounded-full border border-stone-300/80 bg-white/80 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary shadow-sm">
                Créditos y paquetes
            </span>
            <h2 class="mt-4 text-3xl font-black text-stone-900 sm:text-4xl">Precios de paquetes</h2>
            <p class="mx-auto mt-4 max-w-3xl text-base text-stone-600 sm:text-lg">
                Consulta nuestros precios y promociones activas. Un crédito equivale a una sesión.
            </p>
        </div>

        <div class="mx-auto mb-10 max-w-4xl rounded-2xl border border-primary/30 bg-primary/10 p-4 text-center text-sm text-stone-700 sm:p-5 sm:text-base">
            Para poder adquirir un paquete necesitas una cuenta.
            <a href="/clientes/register" class="ml-1 font-bold text-primary underline decoration-primary/40 underline-offset-4 transition hover:decoration-primary">
                Crea una aquí
            </a>
        </div>

        @if($packages->isEmpty())
            <div class="mx-auto max-w-3xl rounded-2xl border border-stone-200 bg-white p-6 text-center shadow-sm">
                <h3 class="text-lg font-bold text-stone-900">No hay paquetes disponibles</h3>
                <p class="mt-2 text-sm text-stone-600">Por ahora no hay paquetes publicados. Si necesitas ayuda, contáctanos.</p>
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
                                <h3 class="mb-4 text-center text-2xl font-black text-stone-900">{{ $package->name }}</h3>
                                <div class="mb-6 text-center">
                                    <x-credit-package-price-display
                                        :base-price="($p ?? [])['base_price'] ?? (float) $package->price"
                                        :final-price="($p ?? [])['final_price'] ?? (float) $package->price"
                                        variant="card"
                                    />
                                </div>

                                <div class="mb-6 rounded-2xl border border-primary/25 bg-primary/10 p-4 text-center">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-600">Incluye</p>
                                    <p class="mt-2 text-3xl font-black text-primary">{{ $package->credits_amount }}</p>
                                    <p class="text-sm font-medium text-stone-700">créditos (clases)</p>
                                </div>
                            </div>

                            <a href="/clientes/register" class="block w-full rounded-xl bg-primary px-6 py-3 text-center text-sm font-bold text-[rgb(255,255,253)] shadow-md transition hover:-translate-y-0.5 hover:shadow-[0_14px_32px_-14px_rgba(94,107,88,0.45)]">
                                adquirir
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
