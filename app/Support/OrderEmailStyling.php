<?php

namespace App\Support;

use App\Models\Order;

class OrderEmailStyling
{
    /**
     * Variables compartidas para plantillas de correo de pedidos (colores, folio, helpers).
     *
     * @return array{
     *     folio: string,
     *     primary: string,
     *     tertiary: string,
     *     paper: string,
     *     stone900: string,
     *     stone600: string,
     *     stone200: string,
     *     statusLabel: Closure,
     *     deliveryLabel: string,
     *     paymentLabel: string,
     *     formatVariants: Closure
     * }
     */
    public static function variables(Order $order): array
    {
        $folio = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);

        $statusLabel = static function (string $status): string {
            return match ($status) {
                'creado' => 'Recibido — pendiente de confirmación de pago',
                'pagado' => 'Pagado',
                'empacado' => 'Empacado / en preparación',
                'entregado' => 'Entregado',
                'cancelado' => 'Cancelado',
                default => $status,
            };
        };

        $deliveryLabel = match ($order->delivery_type) {
            'sucursal' => 'Recogida en sucursal',
            'domicilio' => 'Envío a domicilio',
            default => $order->delivery_type ?? '—',
        };

        $paymentLabel = match ($order->payment_method) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia bancaria',
            'en_linea' => 'Pago en línea',
            default => $order->payment_method ?? '—',
        };

        $formatVariants = static function ($raw): string {
            if (blank($raw)) {
                return '—';
            }
            if (! is_array($raw)) {
                return (string) $raw;
            }
            $parts = [];
            foreach ($raw as $key => $value) {
                if (is_array($value)) {
                    $parts[] = json_encode($value, JSON_UNESCAPED_UNICODE);

                    continue;
                }
                if (is_string($key) && ! is_numeric($key)) {
                    $parts[] = $key.': '.$value;
                } else {
                    $parts[] = (string) $value;
                }
            }

            return $parts === [] ? '—' : implode(' · ', $parts);
        };

        return [
            'folio' => $folio,
            'primary' => '#5e6b58',
            'tertiary' => '#ceb9a0',
            'paper' => '#fffffd',
            'stone900' => '#1c1917',
            'stone600' => '#57534e',
            'stone200' => '#e7e5e4',
            'statusLabel' => $statusLabel,
            'deliveryLabel' => $deliveryLabel,
            'paymentLabel' => $paymentLabel,
            'formatVariants' => $formatVariants,
        ];
    }
}
