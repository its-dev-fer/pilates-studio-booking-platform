<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class WeeklyCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.weekly-calendar-widget';

    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    // Solo visible para admin y empleado
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole(['admin', 'empleado']);
    }

    public function getWeekDataProperty(): array
    {
        $tenant = Filament::getTenant();
        if (!$tenant) return [];

        $capacity = $tenant->capacity_per_slot ?? 5;
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->where('status', 'scheduled')
            ->get();

        $businessHours = collect($tenant->business_hours ?? []);
        $weekDays = [];
        $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeekIso;
            $dayConfig = $businessHours->firstWhere('day', $dayOfWeek);
            $slots = [];

            // Bandera para encontrar la primera clase futura del día
            $foundNextForToday = false; 

            if ($dayConfig && !empty($dayConfig['slots'])) {
                
                // Asegurarnos de que las horas estén en orden cronológico
                $sortedSlots = collect($dayConfig['slots'])->sort()->values()->all();

                foreach ($sortedSlots as $timeString) {
                    $slotTime = Carbon::parse($date->format('Y-m-d') . ' ' . $timeString);
                    $slotEndTime = $slotTime->copy()->addMinutes(59); // Asumimos 1 hora de duración de clase

                    $isPast = false;
                    $isCurrent = false;
                    $isNext = false;

                    // Lógica del Tiempo
                    if ($date->isPast() && !$date->isToday()) {
                        $isPast = true;
                    } elseif ($date->isToday()) {
                        if (now()->greaterThan($slotEndTime)) {
                            $isPast = true;
                        } elseif (now()->between($slotTime, $slotEndTime)) {
                            $isCurrent = true;
                        } elseif (now()->lessThan($slotTime) && !$foundNextForToday) {
                            $isNext = true;
                            $foundNextForToday = true; // Ya encontramos la próxima, no marcamos las demás
                        }
                    }

                    // Conteo y Capacidad
                    $bookedCount = $appointments->filter(function ($app) use ($date, $timeString) {
                        return $app->date->format('Y-m-d') === $date->format('Y-m-d') &&
                               Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                    })->count();

                    $available = $capacity - $bookedCount;
                    
                    if ($available >= 3) $color = 'emerald';
                    elseif ($available > 0) $color = 'amber';
                    else $color = 'red';

                    $slots[] = [
                        'time' => date('h:i A', strtotime($timeString)),
                        'available' => $available,
                        'capacity' => $capacity,
                        'color' => $color,
                        'is_past' => $isPast,
                        'is_current' => $isCurrent,
                        'is_next' => $isNext,
                    ];
                }
            }

            $weekDays[] = [
                'date' => $date->format('d/m/Y'),
                'dayName' => $diasSemana[$dayOfWeek],
                'isToday' => $date->isToday(),
                'slots' => $slots,
            ];
        }

        return $weekDays;
    }
}
