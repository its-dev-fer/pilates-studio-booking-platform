<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(1),

                TextColumn::make('title')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('MXN') // Ajusta a tu moneda
                    ->sortable(),

                // EDITABLE DESDE LA TABLA PARA EMPLEADOS
                TextInputColumn::make('stock')
                    ->label('Stock')
                    ->type('number')
                    ->sortable()
                    ->rules(['required', 'numeric', 'min:0']),

                ToggleColumn::make('is_active')
                    ->label('Activo'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
