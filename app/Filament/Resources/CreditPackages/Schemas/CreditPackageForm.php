<?php

namespace App\Filament\Resources\CreditPackages\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CreditPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('stripe_price_id')
                    ->label('ID de Precio en Stripe')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Ej: price_1Pxxxxx... (Cópialo de tu dashboard de Stripe)'),
                Toggle::make('is_one_time_purchase')
                    ->label('Compra única por usuario')
                    ->helperText('Si está activo, el paquete solo podrá comprarse una vez por usuario y luego se ocultará.')
                    ->default(false),
            ]);
    }
}
