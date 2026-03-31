<?php

namespace App\Filament\Resources\CreditPackages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CreditPackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('credits_amount')->label('Créditos')->sortable()->badge(),
                TextColumn::make('price')->money('mxn')->sortable(),
                IconColumn::make('is_one_time_purchase')
                    ->label('Compra única')
                    ->boolean(),
                TextColumn::make('stripe_product_id')
                    ->label('Stripe producto')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stripe_price_id')
                    ->label('Stripe precio base')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
