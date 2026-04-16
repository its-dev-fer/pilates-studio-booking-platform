<?php

namespace App\Filament\Resources\StoreSections\Schemas;

use App\Models\StoreSection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Sección')->schema([
                    TextInput::make('name')
                        ->label('Nombre de la Sección (Ej. Suplementos)')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->rules(fn (string $operation): array => $operation === 'create'
                            ? [Rule::unique('store_sections', 'slug')]
                            : []),

                    Toggle::make('is_active')
                        ->label('Visible al público')
                        ->default(true),
                ])->columns(2),
            ]);
    }
}
