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
        if (isset($data['time_slot'])) {
            // Convertimos el '09:00:00' de MySQL al formato '09:00' que espera nuestro Select
            $data['time_slot'] = \Carbon\Carbon::parse($data['time_slot'])->format('H:i');
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
