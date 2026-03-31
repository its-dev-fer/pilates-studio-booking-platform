<?php

namespace App\Filament\Resources\CreditPackagePromotions\Concerns;

use App\Models\CreditPackagePromotion;
use Carbon\Carbon;

trait NormalizesCreditPackagePromotionFormData
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizePromotionFormData(array $data): array
    {
        if (($data['type'] ?? '') === CreditPackagePromotion::TYPE_PERCENT) {
            $data['promotional_price'] = null;
        } else {
            $data['discount_percent'] = null;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function assertPromotionWindowValid(array $data, ?int $ignoreId = null): void
    {
        $data = $this->normalizePromotionFormData($data);

        CreditPackagePromotion::validateWindowOrFail(
            (int) $data['credit_package_id'],
            Carbon::parse($data['starts_at']),
            Carbon::parse($data['ends_at']),
            (string) $data['type'],
            isset($data['discount_percent']) ? (string) $data['discount_percent'] : null,
            isset($data['promotional_price']) ? (string) $data['promotional_price'] : null,
            $ignoreId,
        );
    }
}
