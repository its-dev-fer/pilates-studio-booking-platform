<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Closure;
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
                    TextInput::make('transfer_account_number')
                        ->label('Cuenta para transferencias')
                        ->maxLength(23)
                        ->live(onBlur: true)
                        ->formatStateUsing(fn (?string $state): ?string => self::formatTransferAccountNumber($state))
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            $formatted = self::formatTransferAccountNumber($state);

                            if ($formatted !== $state) {
                                $set('transfer_account_number', $formatted);
                            }
                        })
                        ->dehydrateStateUsing(fn (?string $state): ?string => self::digitsOnly($state))
                        ->rule(function (): Closure {
                            return function (string $attribute, mixed $value, Closure $fail): void {
                                $digits = strlen(self::digitsOnly((string) $value));

                                if ($digits < 16 || $digits > 18) {
                                    $fail('La cuenta para transferencias debe contener entre 16 y 18 dígitos.');
                                }
                            };
                        })
                        ->helperText('Opcional. Debe contener entre 16 y 18 dígitos.'),
                ])->columns(2),

                Section::make('Configuraciones Operativas')->schema([
                    TextInput::make('max_appointments_per_day')
                        ->label('Límite de citas por día')
                        ->numeric()
                        ->default(5)
                        ->required()
                        ->helperText('Máximo de personas que pueden agendar en una clase.'),

                    TextInput::make('shipping_fee')
                        ->label('Costo de Envío (E-commerce)')
                        ->numeric()
                        ->default(0.00)
                        ->prefix('$')
                        ->required(),

                    Repeater::make('business_hours')
                        ->label('Programación de Clases por Día')
                        ->schema([
                            Select::make('day')
                                ->label('Día de la semana')
                                ->options([
                                    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
                                    4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
                                ])
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            Select::make('slots')
                                ->label('Horarios de las clases')
                                ->multiple()
                                ->options(function () {
                                    $hours = [];
                                    for ($i = 6; $i <= 21; $i++) {
                                        $time = sprintf('%02d:00', $i);
                                        $hours[$time] = date('h:i A', strtotime($time));
                                    }
                                    return $hours;
                                })
                                ->required()
                                ->helperText('Selecciona todas las horas en las que habrá clase este día.'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->cloneable()
                        ->maxItems(7),
                    ])->columns(2),
            ]);
    }

    protected static function digitsOnly(?string $value): string
    {
        return preg_replace('/\D+/', '', $value ?? '') ?? '';
    }

    protected static function formatTransferAccountNumber(?string $value): string
    {
        $digits = substr(self::digitsOnly($value), 0, 18);

        if ($digits === '') {
            return '';
        }

        if (strlen($digits) <= 16) {
            return trim(chunk_split($digits, 4, ' '));
        }

        return trim(chunk_split($digits, 3, ' '));
    }
}
