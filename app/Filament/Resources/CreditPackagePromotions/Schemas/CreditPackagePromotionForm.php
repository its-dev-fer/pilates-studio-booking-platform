<?php

namespace App\Filament\Resources\CreditPackagePromotions\Schemas;

use App\Models\CreditPackagePromotion;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CreditPackagePromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Paquete y vigencia')
                    ->columns(2)
                    ->schema([
                        Select::make('credit_package_id')
                            ->label('Paquete de créditos')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Solo puede haber una promoción por paquete en el mismo rango de fechas y horas (sin traslapes).'),
                        Select::make('type')
                            ->label('Tipo de promoción')
                            ->options([
                                CreditPackagePromotion::TYPE_PERCENT => 'Porcentaje de descuento sobre el precio base',
                                CreditPackagePromotion::TYPE_FIXED => 'Precio fijo de venta (MXN)',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->helperText('El precio base del paquete se define en Paquetes de créditos. Stripe cobrará el monto resultante en el checkout.'),
                        DateTimePicker::make('starts_at')
                            ->label('Inicio')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->locale('es')
                            ->timezone(config('app.timezone'))
                            ->displayFormat('d/m/Y H:i')
                            ->minutesStep(5)
                            ->closeOnDateSelection(false)
                            ->minDate(today())
                            ->rule('after_or_equal:today')
                            ->live()
                            ->helperText('Puedes elegir cualquier hora del día de hoy (incluso ya pasada). No se permiten días anteriores. Zona horaria de la aplicación.'),
                        DateTimePicker::make('ends_at')
                            ->label('Fin')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->locale('es')
                            ->timezone(config('app.timezone'))
                            ->displayFormat('d/m/Y H:i')
                            ->minutesStep(5)
                            ->closeOnDateSelection(false)
                            ->minDate(fn (Get $get): Carbon|string => $get('starts_at') ?: today())
                            ->rule('after:starts_at')
                            ->helperText('Debe ser posterior al inicio. La promo deja de aplicar después de esta fecha y hora.'),
                    ]),
                Section::make('Valores')
                    ->columns(2)
                    ->schema([
                        TextInput::make('discount_percent')
                            ->label('Porcentaje de descuento')
                            ->suffix('%')
                            ->numeric()
                            ->minValue(0.01)
                            ->maxValue(100)
                            ->step(0.01)
                            ->visible(fn (Get $get): bool => $get('type') === CreditPackagePromotion::TYPE_PERCENT)
                            ->required(fn (Get $get): bool => $get('type') === CreditPackagePromotion::TYPE_PERCENT),
                        TextInput::make('promotional_price')
                            ->label('Precio promocional')
                            ->prefix('$')
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->visible(fn (Get $get): bool => $get('type') === CreditPackagePromotion::TYPE_FIXED)
                            ->required(fn (Get $get): bool => $get('type') === CreditPackagePromotion::TYPE_FIXED)
                            ->helperText('MXN. Sustituye al precio base solo mientras la promoción esté vigente.'),
                    ]),
            ]);
    }
}
