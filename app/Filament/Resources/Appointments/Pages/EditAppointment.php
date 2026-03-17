<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $userId = $data['user_id'] ?? $this->record->user_id;
        $date = $data['date'] ?? $this->record->date;
        $timeSlot = $data['time_slot'] ?? $this->record->time_slot;

        $formattedTime = \Carbon\Carbon::parse($timeSlot)->format('H:i');

        // Verificamos si el cliente ya tiene OTRA cita en ESA MISMA HORA
        $existingAppointment = \App\Models\Appointment::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('time_slot', 'like', $formattedTime . '%')
            ->where('status', 'scheduled')
            ->where('id', '!=', $this->record->id) // Excluimos la cita actual
            ->exists();

        if ($existingAppointment) {
            \Filament\Notifications\Notification::make()
                ->title('Doble reserva detectada')
                ->body('Este cliente ya está inscrito en la clase de las ' . $formattedTime . '. No puedes mover su cita a un horario donde ya tiene un lugar reservado.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
