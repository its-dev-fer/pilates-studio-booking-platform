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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
                        ->rules(fn (string $operation): array => $operation === 'create'
                            ? [Rule::unique('tenants', 'slug')]
                            : [])
                        ->maxLength(255),

                    Textarea::make('address')
                        ->label('Dirección Física')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Datos de cuenta bancaria')->schema([
                    Select::make('transfer_bank_name')
                        ->label('Banco')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options([
                            'BANAMEX' => 'BANAMEX',
                            'BANCOMER' => 'BANCOMER',
                            'BANORTE' => 'BANORTE',
                            'BBVA' => 'BBVA',
                            'HSBC' => 'HSBC',
                            'MERCADO PAGO' => 'MERCADO PAGO',
                            'NU' => 'NU',
                            'OTRO BANCO' => 'OTRO BANCO',
                            'OXXO SPIN' => 'OXXO SPIN',
                        ])
                        ->afterStateHydrated(function (?string $state, Set $set): void {
                            $allowed = [
                                'BANAMEX',
                                'BANCOMER',
                                'BANORTE',
                                'BBVA',
                                'HSBC',
                                'MERCADO PAGO',
                                'NU',
                                'OTRO BANCO',
                                'OXXO SPIN',
                            ];

                            if (filled($state) && ! in_array($state, $allowed, true)) {
                                $set('transfer_bank_name_other', $state);
                                $set('transfer_bank_name', 'OTRO BANCO');
                            }
                        })
                        ->dehydrateStateUsing(fn (?string $state, Get $get): ?string => $state === 'OTRO BANCO'
                            ? trim((string) ($get('transfer_bank_name_other') ?? ''))
                            : $state)
                        ->helperText('Selecciona el banco donde se recibiran transferencias.'),

                    TextInput::make('transfer_bank_name_other')
                        ->label('Otro banco')
                        ->maxLength(120)
                        ->required(fn (Get $get): bool => $get('transfer_bank_name') === 'OTRO BANCO')
                        ->visible(fn (Get $get): bool => $get('transfer_bank_name') === 'OTRO BANCO')
                        ->dehydrated(false)
                        ->helperText('Ingresa el nombre del banco si no aparece en la lista.'),

                    TextInput::make('transfer_account_holder')
                        ->label('Nombre del titular de la cuenta')
                        ->maxLength(255)
                        ->helperText('Nombre completo del titular de la cuenta bancaria.'),

                    TextInput::make('whatsapp_phone')
                        ->label('WhatsApp de sucursal')
                        ->tel()
                        ->maxLength(25)
                        ->helperText('Incluye lada de país. Ejemplo: +529611234567.'),

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
