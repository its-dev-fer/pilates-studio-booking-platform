<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Personales')->schema([
                    TextInput::make('name')
                        ->label('Nombre(s)')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel()
                        ->maxLength(10),
                    TextInput::make('password')
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'edit')
                        ->hiddenOn('create'),
                ])->columns(2),

                Section::make('Accesos y Permisos')->schema([
                    Select::make('roles')
                        ->label('Perfil / Rol')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->required(),

                    Select::make('tenants')
                        ->label('Sucursales Asignadas')
                        ->multiple()
                        ->relationship('tenants', 'name')
                        ->preload()
                        ->helperText('Los Administradores no necesitan estar asignados a una sucursal, tienen acceso global.'),
                ])->columns(2),
            ]);
    }
}
