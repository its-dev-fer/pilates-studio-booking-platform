@php
    extract(\App\Support\OrderEmailStyling::palette(), EXTR_SKIP);
    $emailPageTitle = 'Créditos abonados';
    $branches = collect($user->branchCreditSummary());
    $affectedTenantIds = collect($affectedTenantIds)->map(fn ($id) => (int) $id)->all();
    $totalCredits = $branches->sum('balance');
    $activeBranches = $branches->where('balance', '>', 0);
    $sourceLabel = match ($source) {
        'admin_manual' => 'Asignación manual del equipo administrativo',
        'stripe_purchase' => 'Compra de paquete de créditos con tarjeta',
        'transfer_purchase' => 'Compra de paquete de créditos por transferencia',
        'cash_purchase' => 'Compra de paquete de créditos en efectivo',
        default => 'Abono de créditos',
    };
@endphp
@include('emails.orders.partials.shell-open')
@include('emails.orders.partials.brand-header', ['subtitle' => 'Abono de créditos'])
                <tr>
                    <td style="padding:28px 32px 12px;">
                        <p style="margin:0 0 8px;font-size:18px;font-weight:800;color:{{ $stone900 }};">¡Hola, {{ $user->name }}!</p>
                        <p style="margin:0 0 12px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">
                            Tus créditos fueron abonados correctamente. Ya puedes usarlos para reservar clases en las sucursales correspondientes.
                        </p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:8px;background-color:rgba(94,107,88,0.07);border-radius:12px;border:1px solid rgba(94,107,88,0.18);">
                            <tr>
                                <td style="padding:14px 18px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:{{ $primary }};">Origen del abono</p>
                                    <p style="margin:8px 0 0;font-size:15px;font-weight:700;color:{{ $stone900 }};">{{ $sourceLabel }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.18);border-radius:12px;border:1px solid rgba(94,107,88,0.15);">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Créditos activos</p>
                                    <p style="margin:6px 0 0;font-size:26px;font-weight:800;color:{{ $stone900 }};">{{ $totalCredits }}</p>
                                </td>
                                <td style="padding:16px 20px;text-align:right;vertical-align:top;">
                                    <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Fecha del abono</p>
                                    <p style="margin:6px 0 0;font-size:14px;font-weight:600;color:{{ $stone900 }};">{{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    <p style="margin:4px 0 0;font-size:11px;color:{{ $stone600 }};">Zona: {{ config('app.timezone') }}</p>
                                    <p style="margin:10px 0 0;font-size:11px;color:#a8a29e;">Referencia sistema: usuario ID {{ $user->id }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 16px;">
                        <p style="margin:0 0 12px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:{{ $primary }};">Saldo por sucursal</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid {{ $stone200 }};border-radius:12px;overflow:hidden;">
                            <tr style="background-color:rgba(94,107,88,0.08);">
                                <td style="padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};">Sucursal</td>
                                <td style="padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};text-align:center;">Créditos</td>
                                <td style="padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:{{ $primary }};text-align:right;">Vence</td>
                            </tr>
                            @forelse ($activeBranches as $branch)
                                <tr style="border-top:1px solid {{ $stone200 }};">
                                    <td style="padding:14px 16px;font-size:14px;color:{{ $stone900 }};">
                                        <strong>{{ $branch['tenant_name'] }}</strong>
                                        @if (in_array($branch['tenant_id'], $affectedTenantIds, true))
                                            <span style="display:inline-block;margin-left:8px;padding:2px 8px;border-radius:999px;background-color:rgba(94,107,88,0.12);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:{{ $primary }};">Nuevo abono</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;font-size:16px;font-weight:800;color:{{ $primary }};text-align:center;">{{ $branch['balance'] }}</td>
                                    <td style="padding:14px 16px;font-size:13px;color:{{ $stone600 }};text-align:right;">
                                        {{ $branch['expires_at'] ? \Illuminate\Support\Carbon::parse($branch['expires_at'])->timezone(config('app.timezone'))->format('d/m/Y') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding:18px 16px;font-size:14px;color:{{ $stone600 }};text-align:center;">
                                        No tienes créditos activos en este momento.
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:rgba(206,185,160,0.22);border-radius:12px;border:1px solid rgba(94,107,88,0.2);">
                            <tr>
                                <td style="padding:16px 18px;">
                                    <p style="margin:0 0 8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:{{ $primary }};">Importante</p>
                                    <p style="margin:0;font-size:14px;line-height:1.55;color:{{ $stone900 }};">
                                        Tus créditos <strong>vencen si no los utilizas dentro de los 30 días</strong> posteriores a su asignación. Reserva tus clases a tiempo para no perderlos.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 28px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:{{ $paper }};border:2px dashed {{ $tertiary }};border-radius:14px;">
                            <tr>
                                <td style="padding:22px 24px;text-align:center;">
                                    <p style="margin:0 0 12px;font-size:15px;font-weight:800;color:{{ $primary }};">Reserva tu próxima clase</p>
                                    <p style="margin:0 0 16px;font-size:14px;line-height:1.55;color:{{ $stone600 }};">
                                        Ingresa al portal de clientes para ver tu saldo actualizado y agendar en la sucursal que prefieras.
                                    </p>
                                    <a href="{{ url('/clientes/login') }}" style="display:inline-block;padding:12px 22px;background-color:{{ $primary }};color:{{ $paper }}!important;text-decoration:none;font-size:14px;font-weight:700;border-radius:999px;">Ir al portal de clientes</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
@include('emails.orders.partials.footer', [
    'note' => config('app.name').' · Notificación automática por abono de créditos.',
    'footerCtaUrl' => url('/clientes/login'),
    'footerCtaLabel' => 'Ir al portal de clientes',
])
@include('emails.orders.partials.shell-close')
