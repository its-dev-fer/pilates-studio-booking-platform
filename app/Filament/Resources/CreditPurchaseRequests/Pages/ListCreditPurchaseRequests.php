<?php

namespace App\Filament\Resources\CreditPurchaseRequests\Pages;

use App\Filament\Resources\CreditPurchaseRequests\CreditPurchaseRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListCreditPurchaseRequests extends ListRecords
{
    protected static string $resource = CreditPurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
