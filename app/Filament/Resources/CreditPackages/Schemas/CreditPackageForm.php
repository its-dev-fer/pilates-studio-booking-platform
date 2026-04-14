<?php

namespace App\Filament\Resources\CreditPackages\Schemas;

use App\Models\CreditPackage;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class CreditPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('active_promotion_warning')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->content(function (?CreditPackage $record): ?HtmlString {
                        $promotion = $record?->getActivePromotion();

                        if (! $promotion) {
                            return null;
                        }

                        $startsAt = Carbon::parse($promotion->starts_at)->format('d/m/Y H:i');
                        $endsAt = Carbon::parse($promotion->ends_at)->format('d/m/Y H:i');

                        return new HtmlString(
                            "<div class='rounded-xl border border-warning-300 bg-warning-50 px-4 py-3 text-sm text-warning-900 dark:border-warning-600/70 dark:bg-warning-500/10 dark:text-warning-100'>".
                            "<strong>Promoción activa</strong> (vigente de <strong>{$startsAt}</strong> a <strong>{$endsAt}</strong>). ".
                            'No es posible modificar el precio base ni el precio para nuevos clientes mientras la promoción esté en curso; finalice o espere el fin de la vigencia para actualizar precios.'.
                            '</div>'
                        );
                    })
                    ->visible(fn (?CreditPackage $record): bool => $record?->getActivePromotion() !== null),
                TextInput::make('name')
                    ->label('Nombre del Paquete (Ej. Básico)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('credits_amount')
                    ->label('Cantidad de Créditos')
                    ->numeric()
                    ->required()
                    ->helperText('1 crédito = 1 clase agendada.'),
                TextInput::make('price')
                    ->label('Precio base')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->disabled(fn (?CreditPackage $record): bool => $record?->getActivePromotion() !== null)
                    ->helperText(fn (?CreditPackage $record): string => $record?->getActivePromotion() !== null
                        ? 'MXN. Bloqueado mientras haya una promoción activa para este paquete (incluye sincronización con Stripe).'
                        : 'MXN. Al guardar se sincroniza con Stripe (producto + precio de catálogo). Las promociones aplican otro monto solo en el checkout.'),
                Toggle::make('is_one_time_purchase')
                    ->label('Compra única por usuario')
                    ->helperText('Si está activo, el paquete solo podrá comprarse una vez por usuario y luego se ocultará.')
                    ->default(false),
                Toggle::make('has_new_customer_price')
                    ->label('Precio para nuevos clientes')
                    ->helperText('Aplica solo para cuentas creadas hace máximo 7 días y sin historial previo de compra de créditos.')
                    ->live()
                    ->disabled(fn (?CreditPackage $record): bool => $record?->getActivePromotion() !== null)
                    ->default(false),
                TextInput::make('new_customer_price')
                    ->label('Precio nuevo cliente')
                    ->numeric()
                    ->prefix('$')
                    ->disabled(fn (?CreditPackage $record): bool => $record?->getActivePromotion() !== null)
                    ->minValue(0.01)
                    ->maxValue(fn (Get $get): float => (float) ($get('price') ?: 999999))
                    ->visible(fn (Get $get): bool => (bool) $get('has_new_customer_price'))
                    ->required(fn (Get $get): bool => (bool) $get('has_new_customer_price'))
                    ->helperText('MXN. Si también existe una promoción por fecha, el sistema aplica automáticamente el menor precio para el cliente elegible.'),
            ]);
    }
}
