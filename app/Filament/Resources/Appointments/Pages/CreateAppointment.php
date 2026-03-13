<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserCredit;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    // Validación ANTES de insertar en la base de datos
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = $data['user_id'];
        $date = $data['date'];

        // REGLA: Verificar si el cliente ya tiene una cita ese mismo día (sea en esta sucursal o en otra)
        $existingAppointment = Appointment::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status', 'scheduled')
            ->exists();

        if ($existingAppointment) {
            Notification::make()
                ->title('Doble reserva detectada')
                ->body('Este cliente ya tiene una clase agendada para el ' . \Carbon\Carbon::parse($date)->format('d/m/Y') . '. No se permite más de una clase por día.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt(); // Cancela y devuelve al formulario
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $appointment = $this->record;
        $user = User::find($appointment->user_id);
        $tenantId = Filament::getTenant()->id;

        // 1. Buscar si el cliente tiene créditos activos
        $activeCredit = UserCredit::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->first();

        if ($activeCredit) {
            // Regla A: Descontamos 1 crédito en silencio
            $activeCredit->decrement('balance', 1);

            Notification::make()
                ->title('Crédito descontado')
                ->body('Se descontó 1 crédito de ' . $user->name . ' automáticamente.')
                ->success()
                ->send();
        } else {
            // Regla B: No tiene créditos. Permitimos la cita pero lanzamos alerta roja al empleado.
            // Registramos un crédito especial con balance 0 (entró 1 y se consumió en el acto)
            UserCredit::create([
                'user_id' => $user->id,
                'tenant_id' => $tenantId,
                'balance' => 0,
                'expires_at' => now()->addDays(30),
                'is_special' => true,
            ]);

            Notification::make()
                ->title('¡ATENCIÓN: Cliente sin créditos!')
                ->body('El cliente ' . $user->name . ' NO tenía saldo de créditos. La cita está confirmada, pero debes COBRARLE en mostrador al llegar.')
                ->danger()
                ->persistent() // Obliga al admin a cerrar la notificación manualmente
                ->send();
        }
    }
}
