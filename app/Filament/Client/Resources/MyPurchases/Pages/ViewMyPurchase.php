<?php

namespace App\Filament\Client\Resources\MyPurchases\Pages;

use App\Filament\Client\Resources\MyPurchases\MyPurchasesResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyPurchase extends ViewRecord
{
    protected static string $resource = MyPurchasesResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing(['items', 'tenant']);
    }
}
