<?php

namespace App\Filament\Client\Resources\Appointments;

use App\Filament\Client\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Client\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Client\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Client\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Client\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $recordTitleAttribute = 'appointments';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    public static function getModelLabel(): string
    {
        return 'Mi Cita';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mis Citas';
    }

    // Regla: Los clientes no pueden editar, solo ver y crear
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    // Filtramos para que solo vea SUS propias citas
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
