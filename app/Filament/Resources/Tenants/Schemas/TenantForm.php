<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use illuminate\Support\Str;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información Principal')->schema([
                    TextInput::make('name')
                        ->label('Nombre de la Sucursal')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Textarea::make('address')
                        ->label('Dirección Física')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Configuraciones Operativas')->schema([
                    TextInput::make('max_appointments_per_day')
                        ->label('Límite de citas por día')
                        ->numeric()
                        ->default(20)
                        ->required()
                        ->helperText('Máximo de personas que pueden agendar en un solo día.'),

                    TextInput::make('shipping_fee')
                        ->label('Costo de Envío (E-commerce)')
                        ->numeric()
                        ->default(0.00)
                        ->prefix('$')
                        ->required(),

                    Repeater::make('business_hours')
                        ->label('Horarios de Operación Por Dia')
                        ->schema([
                            Select::make('day')
                                ->label('Día de la semana')
                                ->options([
                                    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                                    4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
                                ])->required(),
                            TimePicker::make('open')
                                ->label('Hora de Apertura')->seconds(false)->required(),
                            TimePicker::make('close')->label('Hora de Cierre')->seconds(false)->required(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->default([
                            ['day' => 1, 'open' => '09:00', 'close' => '18:00'],
                            ['day' => 2, 'open' => '09:00', 'close' => '18:00'],
                            ['day' => 3, 'open' => '09:00', 'close' => '18:00'],
                            ['day' => 4, 'open' => '09:00', 'close' => '18:00'],
                            ['day' => 5, 'open' => '09:00', 'close' => '18:00'],
                        ])
                        ->helperText('Agrega solo los días que la sucursal está abierta.'),
                    ])->columns(2),
            ]);
    }
}
