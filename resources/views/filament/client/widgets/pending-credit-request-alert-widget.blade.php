<div>
    @if ($this->pendingRequest)
        <x-filament::section>
            <div class="pending-credit-alert">
                <div class="pending-credit-alert__head">
                    <div class="pending-credit-alert__title-wrap">
                        <div class="pending-credit-alert__icon-wrap">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"></path>
                                </svg>
                        </div>
                        <div>
                            <h3 class="pending-credit-alert__title">Solicitud de créditos en revisión</h3>
                            <p class="pending-credit-alert__subtitle">Estamos validando tu pago con el equipo administrativo.</p>
                        </div>
                    </div>

                    <span class="pending-credit-alert__badge">Pendiente</span>
                </div>

                <div class="pending-credit-alert__grid">
                    <div class="pending-credit-alert__item">
                        <p class="pending-credit-alert__label">Paquete</p>
                        <p class="pending-credit-alert__value">{{ $this->pendingRequest->package?->name ?? 'N/A' }}</p>
                    </div>

                    <div class="pending-credit-alert__item">
                        <p class="pending-credit-alert__label">Método</p>
                        <p class="pending-credit-alert__value">
                            {{ $this->pendingRequest->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}
                        </p>
                    </div>

                    <div class="pending-credit-alert__item">
                        <p class="pending-credit-alert__label">Precio a pagar</p>
                        <p class="pending-credit-alert__value">
                            <x-credit-package-price-display
                                :base-price="(float) ($this->pendingRequest->quoted_base_price ?? $this->pendingRequest->quoted_final_price ?? $this->pendingRequest->package?->price ?? 0)"
                                :final-price="(float) ($this->pendingRequest->quoted_final_price ?? $this->pendingRequest->package?->price ?? 0)"
                                variant="compact"
                            />
                        </p>
                    </div>

                    <div class="pending-credit-alert__item" style="grid-column: 1 / -1;">
                        <p class="pending-credit-alert__label">Fecha de solicitud</p>
                        <p class="pending-credit-alert__value">{{ $this->pendingRequest->created_at?->format('d/m/Y H:i') }}</p>
                    </div>

                    @if ($this->pendingRequest->payment_method === 'transfer')
                        <div class="pending-credit-alert__item" style="grid-column: 1 / -1;">
                            <p class="pending-credit-alert__label">Cuenta para transferencia</p>
                            <p class="pending-credit-alert__value">
                                {{ $this->formattedTransferAccountNumber }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="pending-credit-alert__foot">
                    Mientras esta solicitud esté pendiente, las opciones de compra de créditos permanecerán bloqueadas.
                </div>
            </div>
        </x-filament::section>
    @endif
</div>
