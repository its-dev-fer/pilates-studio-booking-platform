<?php

namespace App\Filament\Client\Resources\MyPurchases\Schemas;

use App\Models\OrderItem;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MyPurchaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Resumen del pedido')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Número de pedido')
                            ->formatStateUsing(fn (int|string $state): string => '#'.str_pad((string) $state, 5, '0', STR_PAD_LEFT)),

                        TextEntry::make('created_at')
                            ->label('Fecha')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('tenant.name')
                            ->label('Sucursal'),

                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'creado' => 'Recibido',
                                'pagado' => 'Pagado',
                                'empacado' => 'Empacado',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'creado' => 'gray',
                                'pagado' => 'success',
                                'empacado' => 'info',
                                'entregado' => 'success',
                                'cancelado' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('delivery_type')
                            ->label('Tipo de entrega')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'sucursal' => 'Recogida en sucursal',
                                'domicilio' => 'Envío a domicilio',
                                default => '—',
                            }),

                        TextEntry::make('payment_method')
                            ->label('Método de pago')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'efectivo' => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'en_linea' => 'En línea',
                                default => '—',
                            }),

                        TextEntry::make('shipping_address')
                            ->label('Dirección de envío')
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn ($record) => ($record->delivery_type ?? '') === 'domicilio'),

                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('MXN'),

                        TextEntry::make('shipping_fee')
                            ->label('Envío')
                            ->money('MXN')
                            ->placeholder('—'),

                        TextEntry::make('total')
                            ->label('Total')
                            ->money('MXN')
                            ->weight('bold'),
                    ]),

                Section::make('Productos')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->table([
                                TableColumn::make('Producto'),
                                TableColumn::make('Cant.'),
                                TableColumn::make('Precio unit.'),
                                TableColumn::make('Subtotal'),
                            ])
                            ->schema([
                                TextEntry::make('product_title')
                                    ->label('Producto'),

                                TextEntry::make('quantity')
                                    ->label('Cant.'),

                                TextEntry::make('unit_price')
                                    ->label('Precio unit.')
                                    ->money('MXN'),

                                TextEntry::make('line_subtotal')
                                    ->label('Subtotal')
                                    ->getStateUsing(fn (OrderItem $record): float => (float) $record->unit_price * (int) $record->quantity)
                                    ->money('MXN'),
                            ])
                            ->placeholder('Sin líneas en este pedido.'),
                    ]),
            ]);
    }
}
