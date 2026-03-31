<?php

namespace App\Filament\Resources\CreditPackagePromotions\Pages;

use App\Filament\Resources\CreditPackagePromotions\CreditPackagePromotionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreditPackagePromotions extends ListRecords
{
    protected static string $resource = CreditPackagePromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
