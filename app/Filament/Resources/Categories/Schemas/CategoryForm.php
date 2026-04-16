<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Información Principal')->schema([
                        TextInput::make('name')
                            ->label('Nombre (Ej. Proteínas)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->rules(fn (string $operation): array => $operation === 'create'
                                ? [Rule::unique('categories', 'slug')]
                                : []),

                        Select::make('store_section_id')
                            ->relationship('storeSection', 'name') // Debe existir la relación en tu modelo Category
                            ->label('¿A qué Sección pertenece?')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Imagen Decorativa')->schema([
                        FileUpload::make('photo')
                            ->label('Foto de la Categoría')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('categories')
                            ->imageEditor(), // Permite recortar la foto desde el panel
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
