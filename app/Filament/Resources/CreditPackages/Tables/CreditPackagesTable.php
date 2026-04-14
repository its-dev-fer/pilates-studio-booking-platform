<?php

namespace App\Filament\Resources\CreditPackages\Tables;

use App\Models\CreditPackage;
use App\Support\CreditPackagePromotionPricing;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CreditPackagesTable
{
    public static function formatPriceColumn(mixed $state): string
    {
        if ($state === null || $state === '') {
            return '—';
        }

        return '$'.number_format((float) $state, 2, '.', ',');
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('credits_amount')->label('Créditos')->sortable()->badge(),
                TextColumn::make('price')
                    ->label('Precio al público')
                    ->sortable()
                    ->formatStateUsing(function (mixed $state, CreditPackage $record): HtmlString {
                        $pricing = CreditPackagePromotionPricing::resolve($record, now(), null);

                        return new HtmlString(view('components.credit-package-price-display', [
                            'basePrice' => $pricing['base_price'],
                            'finalPrice' => $pricing['final_price'],
                            'variant' => 'table',
                        ])->render());
                    })
                    ->html(),
                IconColumn::make('has_new_customer_price')
                    ->label('Precio para nuevos clientes?')
                    ->boolean(),
                TextColumn::make('new_customer_price')
                    ->label('Precio para nuevos clientes')
                    ->formatStateUsing(fn (mixed $state): string => self::formatPriceColumn($state))
                    ->placeholder('—'),
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
