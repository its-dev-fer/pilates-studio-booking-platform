<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Información General')->schema([
                        TextInput::make('title')
                            ->label('Nombre del Producto')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        RichEditor::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Section::make('Imágenes')->schema([
                        FileUpload::make('images')
                            ->label('Galería del Producto')
                            ->multiple() // Permite subir varias fotos
                            ->image()
                            ->reorderable()
                            ->disk('public') // Mismo disco que Storage::disk('public') en la tienda
                            ->visibility('public')
                            ->directory('products')
                            ->columnSpanFull(),
                    ]),

                    Section::make('Variantes (Tallas, Colores)')->schema([
                        Repeater::make('variants')
                            ->label('Opciones del Producto')
                            ->schema([
                                TextInput::make('option_name')
                                    ->label('Nombre de la Opción (Ej. Talla)')
                                    ->required(),
                                TagsInput::make('option_values')
                                    ->label('Valores (Escribe y presiona Enter)')
                                    ->placeholder('Ej. S, M, L')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['option_name'] ?? null),
                    ]),
                ])->columnSpan(['lg' => 2]),

                // COLUMNA DERECHA (25% del ancho)
                Group::make()->schema([
                    Section::make('Inventario')->schema([
                        TextInput::make('sku')
                            ->label('SKU (Código Interno)')
                            ->required()
                            ->rules([
                                function (Get $get): Unique {
                                    $record = request()->route('record');
                                    $ignoreId = $record instanceof Model ? $record->getKey() : $record;

                                    return Rule::unique('products', 'sku')
                                        ->where('tenant_id', $get('tenant_id'))
                                        ->ignore($ignoreId);
                                },
                            ]),

                        TextInput::make('stock')
                            ->label('Cantidad en Stock')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),

                    Section::make('Precios y Promociones')->schema([
                        TextInput::make('price')
                            ->label('Precio Base')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        TextInput::make('discount_price')
                            ->label('Precio de Oferta')
                            ->numeric()
                            ->prefix('$'),

                        DateTimePicker::make('promo_start_date')
                            ->label('Inicio de Oferta'),

                        DateTimePicker::make('promo_end_date')
                            ->label('Fin de Oferta'),
                    ]),

                    Section::make('Organización')->schema([
                        Select::make('tenant_id')
                            ->label('Sucursal principal (inventario)')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Stock y envío se asocian a esta sucursal.'),

                        Select::make('tenants')
                            ->label('También visible en sucursales')
                            ->relationship('tenants', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Catálogo unificado: el cliente verá en qué sucursales está disponible o puede recoger. La sucursal principal se agrega sola si falta.'),

                        Select::make('category_id')
                            ->label('Categoría Principal')
                            ->relationship('category', 'name')
                            ->preload()
                            ->required(),

                        TextInput::make('catalog_key')
                            ->label('Clave de catálogo unificado')
                            ->maxLength(191)
                            ->helperText('Opcional. Mismo valor en varias sucursales para mostrar un solo producto en la tienda con lista de sucursales (SKU sigue siendo único por sucursal).'),

                        Toggle::make('is_active')
                            ->label('Producto Público')
                            ->default(true),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
