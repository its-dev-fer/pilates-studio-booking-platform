<?php

namespace App\Filament\Resources\CreditPackagePromotions\Pages;

use App\Filament\Resources\CreditPackagePromotions\Concerns\NormalizesCreditPackagePromotionFormData;
use App\Filament\Resources\CreditPackagePromotions\CreditPackagePromotionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCreditPackagePromotion extends EditRecord
{
    use NormalizesCreditPackagePromotionFormData;

    protected static string $resource = CreditPackagePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->assertPromotionWindowValid($data, $this->record->getKey());

        return $this->normalizePromotionFormData($data);
    }
}
