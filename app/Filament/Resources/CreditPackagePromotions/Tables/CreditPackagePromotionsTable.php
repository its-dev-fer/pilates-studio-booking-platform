<?php

namespace App\Filament\Resources\CreditPackagePromotions\Tables;

use App\Models\CreditPackagePromotion;
use App\Support\CreditPackagePromotionPricing;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CreditPackagePromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('package.name')
                    ->label('Paquete')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        CreditPackagePromotion::TYPE_PERCENT => '% Descuento',
                        CreditPackagePromotion::TYPE_FIXED => 'Precio fijo',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        CreditPackagePromotion::TYPE_PERCENT => 'info',
                        CreditPackagePromotion::TYPE_FIXED => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('rule_detail')
                    ->label('Regla')
                    ->getStateUsing(function (CreditPackagePromotion $record): string {
                        if ($record->type === CreditPackagePromotion::TYPE_PERCENT) {
                            return rtrim(rtrim(number_format((float) $record->discount_percent, 2), '0'), '.').'%';
                        }

                        return '$'.number_format((float) $record->promotional_price, 2);
                    }),
                TextColumn::make('package_public_price')
                    ->label('Precio al público (hoy)')
                    ->getStateUsing(function (CreditPackagePromotion $record): HtmlString {
                        $record->loadMissing('package');
                        $pricing = CreditPackagePromotionPricing::resolve($record->package, now(), null);

                        return new HtmlString(view('components.credit-package-price-display', [
                            'basePrice' => $pricing['base_price'],
                            'finalPrice' => $pricing['final_price'],
                            'variant' => 'table',
                        ])->render());
                    })
                    ->html(),
                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status_display')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(fn (CreditPackagePromotion $record): string => $record->statusLabel())
                    ->color(fn (CreditPackagePromotion $record): string => match ($record->statusKey()) {
                        'active' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Alta')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('credit_package_id')
                    ->label('Paquete')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        CreditPackagePromotion::TYPE_PERCENT => 'Porcentaje',
                        CreditPackagePromotion::TYPE_FIXED => 'Precio fijo',
                    ]),
                SelectFilter::make('vigencia')
                    ->label('Vigencia')
                    ->options([
                        'active' => 'En curso',
                        'scheduled' => 'Programadas',
                        'ended' => 'Finalizadas',
                    ])
                    ->modifyQueryUsing(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;
                        if (! filled($value)) {
                            return;
                        }

                        $now = now();

                        if ($value === 'active') {
                            $query->where('starts_at', '<=', $now)->where('ends_at', '>=', $now);
                        } elseif ($value === 'scheduled') {
                            $query->where('starts_at', '>', $now);
                        } elseif ($value === 'ended') {
                            $query->where('ends_at', '<', $now);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
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
