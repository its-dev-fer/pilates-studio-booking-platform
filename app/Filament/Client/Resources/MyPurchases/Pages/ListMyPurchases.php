<?php

namespace App\Filament\Client\Resources\MyPurchases\Pages;

use App\Filament\Client\Resources\MyPurchases\MyPurchasesResource;
use Filament\Resources\Pages\ListRecords;

class ListMyPurchases extends ListRecords
{
    protected static string $resource = MyPurchasesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
