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
        $timeSlot = $data['time_slot'];

        // Formateamos para evitar problemas con los segundos (09:00 vs 09:00:00)
        $formattedTime = \Carbon\Carbon::parse($timeSlot)->format('H:i');

        // REGLA: Verificar si el cliente ya está en ESTA MISMA CLASE
        $existingAppointment = \App\Models\Appointment::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('time_slot', 'like', $formattedTime . '%')
            ->where('status', 'scheduled')
            ->exists();

        if ($existingAppointment) {
            Notification::make()
                ->title('Doble reserva detectada')
                ->body('Este cliente ya está inscrito en la clase de las ' . $formattedTime . '. No puede ocupar dos lugares en el mismo horario.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        $data['booking_origin'] = 'admin_panel';

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
            $appointment->update([
                'payment_method' => 'credit_balance',
            ]);

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

            $appointment->update(['check_in_status' => 'cobrar_al_llegar']);
            $appointment->update([
                'payment_method' => 'cash_at_arrival',
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
