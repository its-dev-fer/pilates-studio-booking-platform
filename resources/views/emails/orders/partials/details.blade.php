                {{-- Sucursal --}}
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Sucursal asociada al pedido</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0;font-size:16px;font-weight:800;color:{{ $stone900 }};">{{ $order->tenant?->name ?? '—' }}</p>
                                    @if(filled($order->tenant?->address))
                                        <p style="margin:8px 0 0;font-size:14px;line-height:1.5;color:{{ $stone600 }};">{{ $order->tenant->address }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{-- Datos de contacto --}}
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Datos de contacto (comprador)</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;">
                            <tr>
                                <td style="padding:16px 18px;">
                                    @if($order->user_id)
                                        <p style="margin:0;font-size:15px;font-weight:700;color:{{ $stone900 }};">{{ $order->user?->name ?? '—' }}</p>
                                        <p style="margin:8px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Correo:</strong> {{ $order->user?->email ?? '—' }}</p>
                                        <p style="margin:6px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Teléfono:</strong> {{ $order->user?->phone ?? '—' }}</p>
                                        <p style="margin:10px 0 0;font-size:12px;color:{{ $stone600 }};">Cuenta vinculada (ID usuario): {{ $order->user_id }}</p>
                                    @else
                                        <p style="margin:0;font-size:15px;font-weight:700;color:{{ $stone900 }};">{{ $order->guest_name ?? '—' }}</p>
                                        <p style="margin:8px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Correo:</strong> {{ $order->guest_email ?? '—' }}</p>
                                        <p style="margin:6px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Teléfono:</strong> {{ $order->guest_phone ?? '—' }}</p>
                                        <p style="margin:10px 0 0;font-size:12px;color:{{ $stone600 }};">Compra como invitado (sin cuenta al momento del pedido).</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{-- Entrega y envío --}}
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Entrega</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Modalidad:</strong> {{ $deliveryLabel }}</p>
                                    @if($order->delivery_type === 'domicilio' && filled($order->shipping_address))
                                        <p style="margin:12px 0 0;font-size:14px;line-height:1.55;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Dirección de envío:</strong><br>{!! nl2br(e($order->shipping_address)) !!}</p>
                                    @elseif($order->delivery_type === 'sucursal')
                                        <p style="margin:12px 0 0;font-size:14px;color:{{ $stone600 }};">Podrás recoger tu pedido en la sucursal indicada arriba cuando te notifiquemos que está listo.</p>
                                    @endif
                                    <p style="margin:12px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Costo de envío (en este pedido):</strong> ${{ number_format((float) $order->shipping_fee, 2) }} MXN</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{-- Pago --}}
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <p style="margin:0 0 10px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Pago</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Método seleccionado:</strong> {{ $paymentLabel }}</p>
                                    @if(filled($order->payment_reference))
                                        <p style="margin:10px 0 0;font-size:14px;color:{{ $stone600 }};"><strong style="color:{{ $stone900 }};">Referencia / folio de pago:</strong> {{ $order->payment_reference }}</p>
                                    @else
                                        <p style="margin:10px 0 0;font-size:13px;color:{{ $stone600 }};">Referencia / folio de pago: <em>(no registrada en el pedido)</em></p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {{-- Productos --}}
                <tr>
                    <td style="padding:8px 32px 20px;">
                        <p style="margin:0 0 12px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Detalle de productos</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;overflow:hidden;">
                            <thead>
                                <tr style="background-color:rgba(94,107,88,0.08);">
                                    <th align="left" style="padding:12px 14px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">Producto</th>
                                    <th align="center" style="padding:12px 10px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">Cant.</th>
                                    <th align="right" style="padding:12px 14px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">P. unit.</th>
                                    <th align="right" style="padding:12px 14px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $line)
                                    @php
                                        $lineTotal = (float) $line->unit_price * (int) $line->quantity;
                                    @endphp
                                    <tr>
                                        <td style="padding:14px;border-top:1px solid {{ $stone200 }};vertical-align:top;">
                                            <p style="margin:0;font-size:15px;font-weight:700;color:{{ $stone900 }};">{{ $line->product_title }}</p>
                                            <p style="margin:6px 0 0;font-size:12px;color:{{ $stone600 }};"><strong>ID producto:</strong> {{ $line->product_id }}</p>
                                            @if($line->relationLoaded('product') && $line->product && filled($line->product->sku))
                                                <p style="margin:4px 0 0;font-size:12px;color:{{ $stone600 }};"><strong>SKU:</strong> {{ $line->product->sku }}</p>
                                            @endif
                                            <p style="margin:6px 0 0;font-size:12px;color:{{ $stone600 }};"><strong>Variantes / opciones:</strong> {{ $formatVariants($line->variant_selected) }}</p>
                                        </td>
                                        <td align="center" style="padding:14px;border-top:1px solid {{ $stone200 }};font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ $line->quantity }}</td>
                                        <td align="right" style="padding:14px;border-top:1px solid {{ $stone200 }};font-size:14px;color:{{ $stone600 }};">${{ number_format((float) $line->unit_price, 2) }}</td>
                                        <td align="right" style="padding:14px;border-top:1px solid {{ $stone200 }};font-size:14px;font-weight:700;color:{{ $stone900 }};">${{ number_format($lineTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                {{-- Totales --}}
                <tr>
                    <td style="padding:8px 32px 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.22);border-radius:12px;border:1px solid rgba(94,107,88,0.12);">
                            <tr>
                                <td style="padding:18px 22px;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="padding:4px 0;font-size:14px;color:{{ $stone600 }};">Subtotal productos</td>
                                            <td align="right" style="padding:4px 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">${{ number_format((float) $order->subtotal, 2) }} MXN</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0;font-size:14px;color:{{ $stone600 }};">Envío / logística</td>
                                            <td align="right" style="padding:4px 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">${{ number_format((float) $order->shipping_fee, 2) }} MXN</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:12px 0 0;border-top:1px solid rgba(94,107,88,0.25);font-size:16px;font-weight:800;color:{{ $primary }};">Total del pedido</td>
                                            <td align="right" style="padding:12px 0 0;border-top:1px solid rgba(94,107,88,0.25);font-size:18px;font-weight:800;color:{{ $primary }};">${{ number_format((float) $order->total, 2) }} MXN</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
