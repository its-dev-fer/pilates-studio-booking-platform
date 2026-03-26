<?php

namespace App\Filament\Resources\StoreSections\Pages;

use App\Filament\Resources\StoreSections\StoreSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoreSections extends ListRecords
{
    protected static string $resource = StoreSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
