@php
    extract(\App\Support\OrderEmailStyling::variables($order), EXTR_SKIP);
    $emailPageTitle = 'Confirmación de compra #'.$folio;
@endphp
@include('emails.orders.partials.shell-open')
@include('emails.orders.partials.brand-header', ['subtitle' => 'Confirmación de compra'])
                {{-- Intro + folio --}}
                <tr>
                    <td style="padding:28px 32px 8px;">
                        <p style="margin:0 0 6px;font-size:13px;color:{{ $stone600 }};">Gracias por tu pedido. Aquí tienes el resumen completo tal como quedó registrado.</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:16px;background-color:rgba(206,185,160,0.18);border-radius:12px;border:1px solid rgba(94,107,88,0.15);">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Número de pedido</p>
                                    <p style="margin:6px 0 0;font-size:26px;font-weight:800;color:{{ $stone900 }};">#{{ $folio }}</p>
                                </td>
                                <td style="padding:16px 20px;text-align:right;vertical-align:top;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Fecha y hora</p>
                                    <p style="margin:6px 0 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $order->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:{{ $stone600 }};">Zona: {{ config('app.timezone') }}</p>
                                    <p style="margin:10px 0 0;font-size:11px;color:#a8a29e;">Referencia sistema: pedido #{{ $order->id }}, sucursal ID {{ $order->tenant_id }}, usuario ID {{ $order->user_id ?? '— (invitado)' }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
@include('emails.orders.partials.status-badge-row')
@include('emails.orders.partials.details')
@if($isGuestCheckout)
@include('emails.orders.partials.guest-cta')
@endif
@include('emails.orders.partials.footer', ['note' => config('app.name').' · Este mensaje es informativo y confirma los datos de tu pedido #'.$folio.'.'])
@include('emails.orders.partials.shell-close')
