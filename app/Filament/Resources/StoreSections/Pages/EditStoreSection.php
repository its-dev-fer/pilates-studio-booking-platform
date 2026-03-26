<?php

namespace App\Filament\Resources\StoreSections\Pages;

use App\Filament\Resources\StoreSections\StoreSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStoreSection extends EditRecord
{
    protected static string $resource = StoreSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
