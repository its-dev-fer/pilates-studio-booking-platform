<?php

namespace App\Filament\Client\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\Appointment;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class ClientStatsWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();
        $tenant = Filament::getTenant(); // Obtenemos la sucursal actual del panel

        // 1. Calcular créditos disponibles en esta sucursal
        $activeCredits = $user->credits()
            ->where('tenant_id', $tenant->id)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->sum('balance');

        // 2. Buscar la próxima cita agendada
        $nextAppointment = Appointment::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->where('status', 'scheduled')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->first();

        $appointmentText = $nextAppointment
            ? $nextAppointment->date->format('d M, Y') . ' a las ' . \Carbon\Carbon::parse($nextAppointment->time_slot)->format('H:i')
            : 'No tienes clases próximas';

        return [
            Stat::make('Créditos Disponibles', $activeCredits)
                ->description('Válidos en ' . $tenant->name)
                ->descriptionIcon('heroicon-m-ticket')
                ->color($activeCredits > 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/comprar-creditos'", // Atajo para comprar
                ]),

            Stat::make('Próxima Clase', $appointmentText)
                ->description($nextAppointment ? '¡Prepárate para entrenar!' : 'Agenda una nueva sesión')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
