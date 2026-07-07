@php
    extract(\App\Support\OrderEmailStyling::palette(), EXTR_SKIP);
    $emailPageTitle = 'Confirmación de cita';
    $appointmentDate = \Carbon\Carbon::parse($appointment->date)->timezone(config('app.timezone'));
    $appointmentTime = \Carbon\Carbon::parse($appointment->time_slot)->timezone(config('app.timezone'));
    $creditRequest = $appointment->creditPurchaseRequest;
    $methodLabel = match ($appointment->payment_method) {
        'transfer' => 'Transferencia bancaria',
        'cash' => 'Efectivo',
        'cash_at_arrival' => 'Efectivo al llegar',
        'stripe' => 'Tarjeta (Stripe)',
        'credit_balance' => 'Crédito de paquete',
        default => 'No especificado',
    };
    $originLabel = match ($appointment->booking_origin) {
        'approved_credit_request' => 'Solicitud de crédito aprobada',
        'stripe_checkout_pending_appointment' => 'Compra de créditos en línea (Stripe)',
        'landing_pending_booking' => 'Reserva desde landing con crédito activo',
        'client_weekly_calendar' => 'Calendario semanal del cliente',
        'client_panel' => 'Panel del cliente',
        'admin_panel' => 'Panel administrativo',
        default => 'No especificado',
    };
    $waDigits = filled($appointment->tenant?->whatsapp_phone)
        ? preg_replace('/\D+/', '', (string) $appointment->tenant->whatsapp_phone)
        : null;
@endphp
@include('emails.orders.partials.shell-open')
@include('emails.orders.partials.brand-header', ['subtitle' => 'Confirmación de reserva'])
                <tr>
                    <td style="padding:28px 32px 12px;">
                        <p style="margin:0 0 8px;font-size:18px;font-weight:800;color:{{ $stone900 }};">¡Hola, {{ $appointment->user->name }}!</p>
                        <p style="margin:0 0 12px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">
                            Tu lugar está asegurado. Hemos confirmado tu cita con los detalles que aparecen a continuación.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.18);border-radius:12px;border:1px solid rgba(94,107,88,0.15);">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Tu clase</p>
                                    <p style="margin:6px 0 0;font-size:26px;font-weight:800;color:{{ $stone900 }};">{{ $appointmentDate->format('d/m/Y') }}</p>
                                    <p style="margin:4px 0 0;font-size:18px;font-weight:700;color:{{ $primary }};">{{ $appointmentTime->format('h:i A') }}</p>
                                </td>
                                <td style="padding:16px 20px;text-align:right;vertical-align:top;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Sucursal</p>
                                    <p style="margin:6px 0 0;font-size:15px;font-weight:700;color:{{ $stone900 }};">{{ $appointment->tenant?->name ?? '—' }}</p>
                                    <p style="margin:12px 0 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Confirmada</p>
                                    <p style="margin:6px 0 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $appointment->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:{{ $stone600 }};">Zona: {{ config('app.timezone') }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;overflow:hidden;">
                            <tr style="background-color:rgba(94,107,88,0.08);">
                                <td colspan="2" style="padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">Detalles de la reserva</td>
                            </tr>
                            <tr style="border-top:1px solid {{ $stone200 }};">
                                <td style="padding:12px 16px;font-size:13px;color:{{ $stone600 }};width:42%;">Cliente</td>
                                <td style="padding:12px 16px;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $appointment->user->name }}</td>
                            </tr>
                            <tr style="border-top:1px solid {{ $stone200 }};">
                                <td style="padding:12px 16px;font-size:13px;color:{{ $stone600 }};">Método de pago</td>
                                <td style="padding:12px 16px;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $methodLabel }}</td>
                            </tr>
                            <tr style="border-top:1px solid {{ $stone200 }};">
                                <td style="padding:12px 16px;font-size:13px;color:{{ $stone600 }};">Origen del registro</td>
                                <td style="padding:12px 16px;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $originLabel }}</td>
                            </tr>
                            <tr style="border-top:1px solid {{ $stone200 }};">
                                <td style="padding:12px 16px;font-size:13px;color:{{ $stone600 }};">Solicitud de crédito</td>
                                <td style="padding:12px 16px;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $creditRequest ? 'Sí' : 'No' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if ($creditRequest)
                <tr>
                    <td style="padding:0 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(94,107,88,0.07);border-radius:12px;border:1px solid rgba(94,107,88,0.18);">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:{{ $primary }};">Solicitud de crédito vinculada</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:{{ $stone900 }};"><strong>Paquete:</strong> {{ $creditRequest->package?->name ?? 'N/A' }}</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:{{ $stone900 }};"><strong>Monto cotizado:</strong> ${{ number_format((float) ($creditRequest->quoted_final_price ?? 0), 2) }}</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:{{ $stone900 }};"><strong>Método en solicitud:</strong> {{ $creditRequest->payment_method === 'transfer' ? 'Transferencia' : ($creditRequest->payment_method === 'cash' ? 'Efectivo' : 'N/A') }}</p>
                                    <p style="margin:0;font-size:14px;color:{{ $stone600 }};"><strong>Fecha de solicitud:</strong> {{ $creditRequest->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                @if ($appointment->payment_method === 'transfer')
                <tr>
                    <td style="padding:0 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.22);border-radius:12px;border:1px solid rgba(94,107,88,0.2);">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:{{ $primary }};">Datos para transferencia</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:{{ $stone900 }};"><strong>Banco:</strong> {{ $appointment->tenant?->transfer_bank_name ?: 'No configurado' }}</p>
                                    <p style="margin:0 0 6px;font-size:14px;color:{{ $stone900 }};"><strong>Titular:</strong> {{ $appointment->tenant?->transfer_account_holder ?: 'No configurado' }}</p>
                                    <p style="margin:0;font-size:14px;color:{{ $stone900 }};"><strong>Cuenta:</strong> {{ $appointment->tenant?->transfer_account_number ?: 'No configurada. Contacta al estudio para validar la cuenta.' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                @if (filled($appointment->tenant?->whatsapp_phone))
                <tr>
                    <td style="padding:0 32px 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:{{ $paper }};border:2px dashed {{ $tertiary }};border-radius:14px;">
                            <tr>
                                <td style="padding:22px 24px;text-align:center;">
                                    <p style="margin:0 0 8px;font-size:15px;font-weight:800;color:{{ $primary }};">¿Necesitas avisar un cambio?</p>
                                    <p style="margin:0 0 14px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">
                                        Notifica cualquier cambio o cancelación por WhatsApp al <strong style="color:{{ $stone900 }};">{{ $appointment->tenant->whatsapp_phone }}</strong>.
                                    </p>
                                    @if (filled($waDigits))
                                    <a href="https://wa.me/{{ $waDigits }}" style="display:inline-block;padding:12px 22px;background-color:{{ $primary }};color:{{ $paper }}!important;text-decoration:none;font-size:14px;font-weight:700;border-radius:999px;">Enviar mensaje por WhatsApp</a>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:0 32px 28px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:{{ $paper }};border:2px dashed {{ $tertiary }};border-radius:14px;">
                            <tr>
                                <td style="padding:22px 24px;text-align:center;">
                                    <p style="margin:0 0 12px;font-size:15px;font-weight:800;color:{{ $primary }};">Consulta tus reservas</p>
                                    <p style="margin:0 0 16px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">
                                        Revisa el detalle de tus clases agendadas desde el portal de clientes.
                                    </p>
                                    <a href="{{ url('/clientes/login') }}" style="display:inline-block;padding:12px 22px;background-color:{{ $primary }};color:{{ $paper }}!important;text-decoration:none;font-size:14px;font-weight:700;border-radius:999px;">Ver mis reservas</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
@include('emails.orders.partials.footer', [
    'note' => config('app.name').' · Confirmación automática de reserva de clase.',
    'footerCtaUrl' => url('/clientes/login'),
    'footerCtaLabel' => 'Ir al portal de clientes',
])
@include('emails.orders.partials.shell-close')
