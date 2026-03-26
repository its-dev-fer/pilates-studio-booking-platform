@php
    extract(\App\Support\OrderEmailStyling::variables($order), EXTR_SKIP);
    $emailPageTitle = 'Actualización pedido #'.$folio;
    $prevHuman = $statusLabel($previousStatus);
    $newHuman = $statusLabel($newStatus);

    $headline = match ($newStatus) {
        'creado' => 'Tu pedido sigue en etapa inicial',
        'pagado' => '¡Registramos tu pago!',
        'empacado' => 'Estamos preparando tu pedido',
        'entregado' => '¡Tu pedido está listo o completado!',
        'cancelado' => 'Actualización importante sobre tu pedido',
        default => 'Hay novedades de tu pedido',
    };

    $bodyParagraphs = match ($newStatus) {
        'creado' => [
            'El estado de tu pedido volvió o se mantiene como <strong>recibido / pendiente de confirmación</strong>. Te avisaremos cuando avance al siguiente paso.',
        ],
        'pagado' => [
            'Confirmamos que el <strong>pago de tu pedido</strong> quedó registrado en nuestro sistema. Gracias por tu confianza.',
            'Continuaremos con la preparación según la modalidad de entrega que elegiste.',
        ],
        'empacado' => $order->delivery_type === 'domicilio'
            ? [
                'Tu pedido está <strong>empacado o en preparación para envío</strong>. Pronto coordinamos la salida hacia la dirección que indicaste.',
            ]
            : [
                'Tu pedido está <strong>empacado o en preparación</strong>. Te notificaremos cuando esté listo para recoger en sucursal.',
            ],
        'entregado' => $order->delivery_type === 'domicilio'
            ? [
                'Marcamos tu pedido como <strong>entregado</strong> (o enviado según corresponda). Si tienes algún inconveniente con la entrega, contáctanos.',
            ]
            : [
                'Tu pedido figura como <strong>entregado o listo para recogida</strong> según lo acordado en sucursal. Si ya pasaste por él, ¡gracias! Si no, revisa horarios con la sucursal.',
            ],
        'cancelado' => [
            'Tu pedido fue <strong>cancelado</strong>. Si no solicitaste esta acción o necesitas ayuda, responde a este correo o comunícate con la sucursal lo antes posible.',
        ],
        default => [
            'Hubo un cambio en el seguimiento de tu pedido. Revisa el resumen a continuación con todos los datos actualizados.',
        ],
    };
@endphp
@include('emails.orders.partials.shell-open')
@include('emails.orders.partials.brand-header', ['subtitle' => 'Actualización de tu pedido'])
                {{-- Mensaje según etapa --}}
                <tr>
                    <td style="padding:28px 32px 12px;">
                        <p style="margin:0 0 8px;font-size:18px;font-weight:800;color:{{ $stone900 }};">{{ $headline }}</p>
                        @foreach($bodyParagraphs as $p)
                            <p style="margin:0 0 12px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">{!! $p !!}</p>
                        @endforeach
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:8px;background-color:rgba(94,107,88,0.07);border-radius:12px;border:1px solid rgba(94,107,88,0.18);">
                            <tr>
                                <td style="padding:14px 18px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:{{ $primary }};">Cambio de estado</p>
                                    <p style="margin:8px 0 0;font-size:14px;color:{{ $stone900 }};">
                                        <span style="color:{{ $stone600 }};">Antes:</span> {{ $prevHuman }}
                                    </p>
                                    <p style="margin:6px 0 0;font-size:15px;font-weight:700;color:{{ $primary }};">
                                        Ahora: {{ $newHuman }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{-- Folio + fechas --}}
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.18);border-radius:12px;border:1px solid rgba(94,107,88,0.15);">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Número de pedido</p>
                                    <p style="margin:6px 0 0;font-size:26px;font-weight:800;color:{{ $stone900 }};">#{{ $folio }}</p>
                                </td>
                                <td style="padding:16px 20px;text-align:right;vertical-align:top;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Pedido realizado</p>
                                    <p style="margin:6px 0 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $order->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    <p style="margin:12px 0 0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Última actualización</p>
                                    <p style="margin:6px 0 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $order->updated_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
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
@include('emails.orders.partials.footer', ['note' => config('app.name').' · Notificación automática por cambio de estado del pedido #'.$folio.'.'])
@include('emails.orders.partials.shell-close')
