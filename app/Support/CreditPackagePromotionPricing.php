<?php

namespace App\Support;

use App\Models\CreditPackage;
use App\Models\CreditPackagePromotion;
use App\Models\User;
use Carbon\Carbon;

class CreditPackagePromotionPricing
{
    /**
     * @param  array{base_price: float, final_price: float}  $pricing
     */
    public static function hasDisplayDiscount(array $pricing): bool
    {
        return abs($pricing['base_price'] - $pricing['final_price']) > 0.009;
    }

    /**
     * @return array{
     *     base_price: float,
     *     final_price: float,
     *     promotion: ?CreditPackagePromotion,
     *     applied_label: ?string,
     *     has_new_customer_price: bool
     * }
     */
    public static function resolve(CreditPackage $package, ?Carbon $at = null, ?User $user = null): array
    {
        $at ??= now();
        $base = (float) $package->price;

        /** @var CreditPackagePromotion|null $promotion */
        $promotion = CreditPackagePromotion::query()
            ->where('credit_package_id', $package->id)
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>=', $at)
            ->orderByDesc('starts_at')
            ->first();

        if (! $promotion) {
            $newCustomerPrice = self::resolveNewCustomerPrice($package, $user);
            $final = $newCustomerPrice ?? $base;

            return [
                'base_price' => $base,
                'final_price' => $final,
                'promotion' => null,
                'applied_label' => $newCustomerPrice !== null ? 'Precio nuevo cliente' : null,
                'has_new_customer_price' => $newCustomerPrice !== null,
            ];
        }

        if ($promotion->type === CreditPackagePromotion::TYPE_PERCENT) {
            $pct = (float) $promotion->discount_percent;
            $pct = max(0.0, min(100.0, $pct));
            $final = round($base * (1 - $pct / 100), 2);
        } else {
            $final = round((float) $promotion->promotional_price, 2);
        }

        $final = max(0.01, $final);

        $newCustomerPrice = self::resolveNewCustomerPrice($package, $user);
        if ($newCustomerPrice !== null && $newCustomerPrice < $final) {
            $final = $newCustomerPrice;
            $appliedLabel = 'Precio nuevo cliente';
            $hasNewCustomerPrice = true;
        } else {
            $appliedLabel = $promotion->type === CreditPackagePromotion::TYPE_PERCENT
                ? 'Promoción por porcentaje'
                : 'Promoción por precio fijo';
            $hasNewCustomerPrice = $newCustomerPrice !== null;
        }

        return [
            'base_price' => $base,
            'final_price' => $final,
            'promotion' => $promotion,
            'applied_label' => $appliedLabel,
            'has_new_customer_price' => $hasNewCustomerPrice,
        ];
    }

    /**
     * Líneas para Cashier: Price de catálogo (precio base) o monto dinámico ligado al mismo producto Stripe si hay promo.
     *
     * @return array<int|string, mixed>
     */
    public static function checkoutLineItems(CreditPackage $package, ?Carbon $at = null, ?User $user = null): array
    {
        $pricing = self::resolve($package, $at, $user);
        $hasCustomPrice = abs($pricing['final_price'] - $pricing['base_price']) > 0.001;

        if ($hasCustomPrice) {
            return [self::stripeLineItem($package, $pricing['final_price'])];
        }

        if (! $package->stripe_price_id) {
            throw new \RuntimeException(
                'Este paquete no está enlazado a Stripe. Edita el paquete en el panel y guarda para sincronizar, o revisa la configuración de Stripe.'
            );
        }

        return [$package->stripe_price_id => 1];
    }

    /**
     * Una línea de checkout con importe distinto al precio de catálogo (promoción), asociada al producto Stripe del paquete.
     *
     * @return array<string, mixed>
     */
    public static function stripeLineItem(CreditPackage $package, float $finalPriceMxn): array
    {
        $line = [
            'price_data' => [
                'currency' => 'mxn',
                'unit_amount' => (int) round($finalPriceMxn * 100),
            ],
            'quantity' => 1,
        ];

        if ($package->stripe_product_id) {
            $line['price_data']['product'] = $package->stripe_product_id;
        } else {
            $line['price_data']['product_data'] = [
                'name' => $package->name.' (promoción)',
                'metadata' => [
                    'credit_package_id' => (string) $package->id,
                ],
            ];
        }

        return $line;
    }

    private static function resolveNewCustomerPrice(CreditPackage $package, ?User $user): ?float
    {
        if (! $user || ! $package->has_new_customer_price || ! $user->isNewCreditCustomer()) {
            return null;
        }

        $price = (float) $package->new_customer_price;

        return $price > 0 ? round($price, 2) : null;
    }
}
