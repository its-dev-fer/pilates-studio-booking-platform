<?php

namespace App\Filament\Client\Resources\Appointments\Pages;

use App\Filament\Client\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\UserCredit;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        $user = auth()->user();
        $tenantId = Filament::getTenant()->id;

        $activeCredits = UserCredit::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');

        if ($activeCredits > 0) {
            return new HtmlString("<span class='text-primary font-bold'>Tienes {$activeCredits} créditos disponibles en esta sucursal.</span> Se descontará 1 al agendar.");
        }

        return new HtmlString("<span class='text-red-600 font-bold'>No tienes créditos activos en esta sucursal.</span> <a href='/comprar-creditos' class='underline hover:text-red-800'>Compra un paquete aquí</a> antes de intentar agendar.");
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $tenantId = Filament::getTenant()->id;
        $date = $data['date'];

        $existingAppointment = Appointment::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('status', 'scheduled') // Solo contamos las activas, si canceló antes, sí puede volver a agendar
            ->exists();

        if ($existingAppointment) {
            Notification::make()
                ->title('Límite de citas diario')
                ->body('Ya tienes una clase agendada para este día. Por favor, selecciona otra fecha.')
                ->danger()
                ->send();

            $this->halt(); // Cancela la creación
        }

        $activeCredit = UserCredit::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->first();

        // Si no tiene créditos, detenemos el proceso (Halt) y mostramos error
        if (!$activeCredit) {
            Notification::make()
                ->title('Sin créditos suficientes')
                ->body('No tienes créditos activos para agendar. Por favor, adquiere un paquete.')
                ->danger()
                ->send();

            $this->halt(); // Cancela la creación en la base de datos
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();
        $tenantId = Filament::getTenant()->id;

        $activeCredit = UserCredit::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->first();

        if ($activeCredit) {
            $activeCredit->decrement('balance', 1);

            Notification::make()
                ->title('¡Clase Agendada!')
                ->body('Se ha descontado 1 crédito. ¡Te esperamos!')
                ->success()
                ->send();
        }
    }
}
