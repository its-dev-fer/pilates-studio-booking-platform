<?php

namespace App\Filament\Client\Resources\MyPurchases\Tables;

use App\Filament\Client\Resources\MyPurchases\MyPurchasesResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MyPurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Pedido')
                    ->formatStateUsing(fn (int|string $state): string => '#'.str_pad((string) $state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Sucursal')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
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

                TextColumn::make('delivery_type')
                    ->label('Entrega')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'sucursal' => 'Recogida en sucursal',
                        'domicilio' => 'Envío a domicilio',
                        default => '—',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('ver')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record): string => MyPurchasesResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([])
            ->emptyStateHeading('Aún no tienes compras')
            ->emptyStateDescription('Cuando compres en la tienda, tus pedidos aparecerán aquí (en la sucursal seleccionada arriba).');
    }
}
