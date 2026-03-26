<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = Filament::getTenant();

        if ($tenant) {
            $data['tenant_id'] = $tenant->id;
        }

        // products.category_id is required in DB; use first selected category from the multiselect.
        if (empty($data['category_id']) && ! empty($data['categories']) && is_array($data['categories'])) {
            $data['category_id'] = reset($data['categories']);
        }

        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }
}
