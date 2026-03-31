<?php

namespace App\Filament\Resources\CreditPackagePromotions\Schemas;

use App\Models\CreditPackagePromotion;
use App\Support\CreditPackagePromotionPricing;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreditPackagePromotionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Paquete')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('package.name')
                            ->label('Nombre'),
                        TextEntry::make('package.price')
                            ->label('Precio base')
                            ->money('mxn'),
                        TextEntry::make('package.credits_amount')
                            ->label('Créditos incluidos'),
                    ]),
                Section::make('Promoción')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                CreditPackagePromotion::TYPE_PERCENT => 'Porcentaje',
                                CreditPackagePromotion::TYPE_FIXED => 'Precio fijo',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                CreditPackagePromotion::TYPE_PERCENT => 'info',
                                CreditPackagePromotion::TYPE_FIXED => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('rule_summary')
                            ->label('Regla')
                            ->getStateUsing(fn (CreditPackagePromotion $record): string => $record->ruleSummary()),
                        TextEntry::make('discount_percent')
                            ->label('Descuento %')
                            ->placeholder('—')
                            ->formatStateUsing(fn (?string $state): string => $state !== null && $state !== '' ? $state.'%' : '—')
                            ->visible(fn (CreditPackagePromotion $record): bool => $record->type === CreditPackagePromotion::TYPE_PERCENT),
                        TextEntry::make('promotional_price')
                            ->label('Precio fijo')
                            ->money('mxn')
                            ->placeholder('—')
                            ->visible(fn (CreditPackagePromotion $record): bool => $record->type === CreditPackagePromotion::TYPE_FIXED),
                        TextEntry::make('starts_at')
                            ->label('Inicio')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('ends_at')
                            ->label('Fin')
                            ->dateTime('d/m/Y H:i'),
                    ]),
                Section::make('Estado y cobro')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status_display')
                            ->label('Estado de vigencia')
                            ->badge()
                            ->getStateUsing(fn (CreditPackagePromotion $record): string => $record->statusLabel())
                            ->color(fn (CreditPackagePromotion $record): string => match ($record->statusKey()) {
                                'active' => 'success',
                                'scheduled' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('public_price_hint')
                            ->label('Monto que vería el cliente ahora')
                            ->columnSpanFull()
                            ->getStateUsing(function (CreditPackagePromotion $record): string {
                                $now = now();

                                if ($now->lt($record->starts_at)) {
                                    return 'Aún no aplica: inicia el '.$record->starts_at->format('d/m/Y H:i').'.';
                                }

                                if ($now->gt($record->ends_at)) {
                                    return 'La promoción ya finalizó el '.$record->ends_at->format('d/m/Y H:i').'.';
                                }

                                $record->loadMissing('package');
                                $pricing = CreditPackagePromotionPricing::resolve($record->package, $now);
                                $base = number_format($pricing['base_price'], 2);
                                $final = number_format($pricing['final_price'], 2);

                                if ($pricing['promotion'] && (int) $pricing['promotion']->getKey() === (int) $record->getKey()) {
                                    return $final.' MXN (precio base del paquete: '.$base.' MXN). Este importe es el que se usa en la página de créditos y en Stripe.';
                                }

                                return 'En este instante otra regla podría estar aplicando; revisa el listado de promociones del mismo paquete.';
                            }),
                        TextEntry::make('created_at')
                            ->label('Creada')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Actualizada')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
