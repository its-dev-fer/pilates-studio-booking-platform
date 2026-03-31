<?php

namespace App\Services;

use App\Models\CreditPackage;
use InvalidArgumentException;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class StripeCreditPackageProductService
{
    public const CURRENCY = 'mxn';

    /**
     * Crea o actualiza el producto en Stripe y asegura un Price cuyo monto coincide con {@see CreditPackage::$price}.
     */
    public function syncBasePriceWithStripe(CreditPackage $package): void
    {
        $stripe = Cashier::stripe();

        $unitAmount = (int) round(((float) $package->price) * 100);
        if ($unitAmount < 1) {
            throw new InvalidArgumentException('El precio del paquete debe ser mayor a cero.');
        }

        if (! $package->stripe_product_id && $package->stripe_price_id) {
            try {
                $existingPrice = $stripe->prices->retrieve($package->stripe_price_id);
                $package->forceFill([
                    'stripe_product_id' => is_string($existingPrice->product)
                        ? $existingPrice->product
                        : $existingPrice->product->id,
                ])->saveQuietly();
            } catch (ApiErrorException) {
                // Se creará un producto nuevo abajo.
            }
        }

        if (! $package->stripe_product_id) {
            $product = $stripe->products->create([
                'name' => $package->name,
                'metadata' => [
                    'credit_package_id' => (string) $package->id,
                ],
            ]);
            $package->forceFill(['stripe_product_id' => $product->id])->saveQuietly();
        } else {
            $stripe->products->update($package->stripe_product_id, [
                'name' => $package->name,
                'metadata' => [
                    'credit_package_id' => (string) $package->id,
                ],
            ]);
        }

        $needsNewPrice = $package->stripe_price_id === null;
        if (! $needsNewPrice) {
            try {
                $currentPrice = $stripe->prices->retrieve($package->stripe_price_id);
                $sameAmount = (int) $currentPrice->unit_amount === $unitAmount;
                $sameCurrency = strtolower((string) $currentPrice->currency) === self::CURRENCY;
                if (! $sameAmount || ! $sameCurrency) {
                    $needsNewPrice = true;
                }
            } catch (ApiErrorException) {
                $needsNewPrice = true;
            }
        }

        if ($needsNewPrice) {
            $price = $stripe->prices->create([
                'product' => $package->stripe_product_id,
                'currency' => self::CURRENCY,
                'unit_amount' => $unitAmount,
            ]);
            $package->forceFill(['stripe_price_id' => $price->id])->saveQuietly();
        }
    }
}
