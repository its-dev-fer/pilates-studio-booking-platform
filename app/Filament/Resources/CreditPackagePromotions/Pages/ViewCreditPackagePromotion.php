<?php

namespace App\Filament\Resources\CreditPackagePromotions\Pages;

use App\Filament\Resources\CreditPackagePromotions\CreditPackagePromotionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCreditPackagePromotion extends ViewRecord
{
    protected static string $resource = CreditPackagePromotionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing(['package']);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
