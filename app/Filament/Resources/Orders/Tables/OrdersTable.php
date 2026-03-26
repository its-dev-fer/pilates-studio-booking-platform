<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Folio')
                    ->sortable()
                    ->searchable()
                    ->prefix('#000'),

                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->default(fn (Model $record) => $record->guest_name.' (Invitado)')
                    ->searchable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'creado' => 'gray',
                        'pagado', 'entregado' => 'success',
                        'empacado' => 'info',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
