<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Operación';
    }

    public static function getModelLabel(): string
    {
        return 'Cita';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Citas';
    }

    protected static ?string $recordTitleAttribute = 'appointment';

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        // 1. Si es Administrador, tiene poder absoluto. Puede editar lo que sea.
        if ($user->hasRole('admin')) {
            return true;
        }

        // 2. Si es Empleado, verificamos si el cliente ya llegó
        if ($record->check_in_status === 'cliente_llego' || $record->status === 'completed') {
            return false;
        }

        // 3. Verificamos si la fecha y hora de la cita ya pasaron
        $appointmentTime = \Carbon\Carbon::parse($record->date->format('Y-m-d') . ' ' . $record->time_slot);
        if ($appointmentTime->isPast()) {
            return false;
        }

        // Si sobrevive a los filtros, el empleado puede editar
        return true;
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
