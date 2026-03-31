<?php

namespace App\Filament\Resources\CreditPackagePromotions\Pages;

use App\Filament\Resources\CreditPackagePromotions\Concerns\NormalizesCreditPackagePromotionFormData;
use App\Filament\Resources\CreditPackagePromotions\CreditPackagePromotionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditPackagePromotion extends CreateRecord
{
    use NormalizesCreditPackagePromotionFormData;

    protected static string $resource = CreditPackagePromotionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->assertPromotionWindowValid($data, null);

        return $this->normalizePromotionFormData($data);
    }
}
