<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([

                    Section::make('Información del Cliente')->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Cliente Registrado')
                            ->searchable()
                            ->preload()
                            ->live() // Hace que la vista reaccione al instante
                            ->helperText('Déjalo en blanco si es un cliente invitado.'),

                        // Datos de invitado (Solo se muestran si NO se seleccionó un cliente)
                        Grid::make(3)->schema([
                            TextInput::make('guest_name')
                                ->label('Nombre (Invitado)')
                                ->required(fn (Get $get) => blank($get('user_id'))),
                            TextInput::make('guest_email')
                                ->label('Correo')
                                ->email(),
                            TextInput::make('guest_phone')
                                ->label('Teléfono'),
                        ])->hidden(fn (Get $get) => filled($get('user_id'))),
                    ]),

                    Section::make('Productos (Carrito)')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'title')
                                    ->label('Producto')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(onBlur: true)
                                    // MAGIA: Al elegir el producto, traemos su precio y nombre automáticamente
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $precioReal = $product->discount_price ?? $product->price;
                                            $set('unit_price', $precioReal);
                                            $set('product_title', $product->title);
                                        }
                                    }),

                                Hidden::make('product_title'), // Para congelar el nombre en el historial

                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true),

                                TextInput::make('unit_price')
                                    ->label('Precio Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar otro producto'),
                    ]),

                    Section::make('Logística')->schema([
                        Select::make('delivery_type')
                            ->label('Método de Entrega')
                            ->options([
                                'sucursal' => 'Recogida en Sucursal',
                                'domicilio' => 'Envío a Domicilio',
                            ])
                            ->required()
                            ->live(),

                        Textarea::make('shipping_address')
                            ->label('Dirección de Envío Completa')
                            ->required(fn (Get $get) => $get('delivery_type') === 'domicilio')
                            ->hidden(fn (Get $get) => $get('delivery_type') !== 'domicilio'),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Finanzas y Cobro')->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        TextInput::make('shipping_fee')
                            ->label('Costo de Envío')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->live(onBlur: true),

                        TextInput::make('total')
                            ->label('TOTAL A COBRAR')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->helperText('Suma manual del subtotal + envío.'),

                        Select::make('payment_method')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo en Sucursal',
                                'transferencia' => 'Transferencia Bancaria',
                                'en_linea' => 'Pago en Línea (Stripe/MercadoPago)',
                            ])
                            ->required(),

                        TextInput::make('payment_reference')
                            ->label('Folio o Referencia')
                            ->helperText('Folio del ticket, terminación de tarjeta o folio de transferencia.'),

                        Select::make('status')
                            ->label('Estatus del Pedido')
                            ->options([
                                'creado' => 'Creado (Pendiente)',
                                'pagado' => 'Pagado',
                                'empacado' => 'Empacado / Listo para recoger',
                                'entregado' => 'Entregado (Completado)',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('creado')
                            ->required()
                            ->selectablePlaceholder(false),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
