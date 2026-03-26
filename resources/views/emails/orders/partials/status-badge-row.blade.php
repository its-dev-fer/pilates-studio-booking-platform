                <tr>
                    <td style="padding:8px 32px 16px;">
                        <p style="margin:0 0 6px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:{{ $primary }};">Estado del pedido</p>
                        <p style="margin:0;padding:10px 14px;display:inline-block;background-color:{{ $paper }};border:2px solid {{ $primary }};border-radius:999px;font-size:14px;font-weight:700;color:{{ $primary }};">{{ $statusLabel($order->status) }}</p>
                    </td>
                </tr>
