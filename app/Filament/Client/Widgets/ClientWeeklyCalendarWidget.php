<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use App\Models\CreditPurchaseRequest;
use App\Models\UserCredit;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class ClientWeeklyCalendarWidget extends Widget
{
    protected string $view = 'filament.client.widgets.client-weekly-calendar-widget';

    protected static ?int $sort = 1; // Que aparezca hasta arriba en su panel
    protected int | string | array $columnSpan = 'full';

    // REGLA: Solo visible para clientes
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('cliente') ?? false;
    }

    public function getWeekDataProperty(): array
    {
        $tenant = Filament::getTenant();
        if (!$tenant) return [];

        $capacity = $tenant->capacity_per_slot ?? 5;
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $user = auth()->user();

        // Traemos TODAS las citas de la semana para contar la capacidad
        $allAppointments = Appointment::where('tenant_id', $tenant->id)
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->where('status', 'scheduled')
            ->get();
        $pendingRequests = CreditPurchaseRequest::query()
            ->where('requested_tenant_id', $tenant->id)
            ->whereBetween('requested_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->whereIn('payment_method', [CreditPurchaseRequest::METHOD_TRANSFER, CreditPurchaseRequest::METHOD_CASH])
            ->whereNotNull('requested_time_slot')
            ->get();

        $businessHours = collect($tenant->business_hours ?? []);
        $weekDays = [];
        $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeekIso;
            $dayConfig = $businessHours->firstWhere('day', $dayOfWeek);
            $slots = [];

            if ($dayConfig && !empty($dayConfig['slots'])) {
                $sortedSlots = collect($dayConfig['slots'])->sort()->values()->all();

                foreach ($sortedSlots as $timeString) {
                    $slotTime = Carbon::parse($date->format('Y-m-d') . ' ' . $timeString);
                    
                    // ¿Ya pasó la clase?
                    $isPast = $date->isPast() && !$date->isToday() || ($date->isToday() && now()->greaterThan($slotTime));

                    // Contar ocupación
                    $bookedCount = $allAppointments->filter(function ($app) use ($date, $timeString) {
                        return $app->date->format('Y-m-d') === $date->format('Y-m-d') &&
                               Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                    })->count();
                    $heldByPendingRequests = $pendingRequests->filter(function (CreditPurchaseRequest $request) use ($date, $timeString) {
                        return Carbon::parse((string) $request->requested_date)->format('Y-m-d') === $date->format('Y-m-d')
                            && Carbon::parse((string) $request->requested_time_slot)->format('H:i') === $timeString;
                    })->count();

                    // ¿El cliente actual ya está en esta clase?
                    $isBookedByMe = $allAppointments->contains(function ($app) use ($date, $timeString, $user) {
                        return $app->user_id === $user->id &&
                               $app->date->format('Y-m-d') === $date->format('Y-m-d') &&
                               Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                    });

                    $available = $capacity - ($bookedCount + $heldByPendingRequests);
                    
                    if ($available >= 3) $color = 'emerald';
                    elseif ($available > 0) $color = 'amber';
                    else $color = 'red';

                    $slots[] = [
                        'time_raw' => $timeString,
                        'time_formatted' => date('h:i A', strtotime($timeString)),
                        'available' => $available,
                        'capacity' => $capacity,
                        'color' => $color,
                        'is_past' => $isPast,
                        'is_booked_by_me' => $isBookedByMe,
                    ];
                }
            }

            $weekDays[] = [
                'date_raw' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('d/m/Y'),
                'dayName' => $diasSemana[$dayOfWeek],
                'isToday' => $date->isToday(),
                'slots' => $slots,
            ];
        }

        return $weekDays;
    }

    public function bookClass($date, $time)
    {
        $user = auth()->user();
        $tenant = Filament::getTenant();

        // 1. Validar si ya la tiene
        $exists = Appointment::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('time_slot', 'like', Carbon::parse($time)->format('H:i') . '%')
            ->where('status', 'scheduled')
            ->exists();

        if ($exists) {
            Notification::make()->title('Ya estás inscrito en esta clase.')->warning()->send();
            return;
        }

        // 2. Validar Créditos
        $activeCredit = UserCredit::where('user_id', $user->id)->where('balance', '>', 0)->first();

        if (!$activeCredit) {
            Notification::make()
                ->title('Créditos Insuficientes')
                ->body('No tienes paquetes activos. Por favor adquiere uno para poder reservar.')
                ->warning()
                ->send();
            
            $this->redirect('/comprar-creditos');
            return;
        }

        // 3. Crear Reserva y Descontar
        $activeCredit->decrement('balance');

        Appointment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'date' => $date,
            'time_slot' => $time,
            'status' => 'scheduled',
            'check_in_status' => 'pendiente',
        ]);

        Notification::make()
            ->title('¡Lugar Asegurado!')
            ->body('Hemos descontado 1 crédito. Nos vemos en clase.')
            ->success()
            ->send();
    }
}
