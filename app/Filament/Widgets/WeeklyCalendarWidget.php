<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\CreditPurchaseRequest;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class WeeklyCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.weekly-calendar-widget';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public string $viewMode = 'week';

    public string $anchorDate;

    public function mount(): void
    {
        $this->anchorDate = now()->toDateString();
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasRole(['admin', 'empleado']);
    }

    public function setViewMode(string $mode): void
    {
        if (! in_array($mode, ['week', 'month', 'today'], true)) {
            return;
        }

        $this->viewMode = $mode;

        if ($mode === 'today') {
            $this->anchorDate = now()->toDateString();
        }
    }

    public function goToToday(): void
    {
        $this->anchorDate = now()->toDateString();
    }

    public function previousPeriod(): void
    {
        $date = Carbon::parse($this->anchorDate);

        $this->anchorDate = match ($this->viewMode) {
            'month' => $date->subMonth()->toDateString(),
            'today' => $date->subDay()->toDateString(),
            default => $date->subWeek()->toDateString(),
        };
    }

    public function nextPeriod(): void
    {
        $date = Carbon::parse($this->anchorDate);

        $this->anchorDate = match ($this->viewMode) {
            'month' => $date->addMonth()->toDateString(),
            'today' => $date->addDay()->toDateString(),
            default => $date->addWeek()->toDateString(),
        };
    }

    public function getPeriodTitleProperty(): string
    {
        $anchor = Carbon::parse($this->anchorDate);

        return match ($this->viewMode) {
            'today' => $anchor->translatedFormat('j'),
            'month', 'week' => ucfirst($anchor->translatedFormat('F Y')),
        };
    }

    public function getPeriodSubtitleProperty(): string
    {
        $anchor = Carbon::parse($this->anchorDate);

        return match ($this->viewMode) {
            'today' => ucfirst($anchor->translatedFormat('l, j \d\e F \d\e Y')),
            'month' => 'Vista mensual',
            default => 'Semana del '.$anchor->copy()->startOfWeek()->translatedFormat('j \d\e F')
                .' al '.$anchor->copy()->endOfWeek()->translatedFormat('j \d\e F'),
        };
    }

    public function getTodayReferenceProperty(): string
    {
        return 'Hoy: '.ucfirst(now()->translatedFormat('l, j \d\e F \d\e Y'));
    }

    public function getIsViewingCurrentPeriodProperty(): bool
    {
        $anchor = Carbon::parse($this->anchorDate);

        return match ($this->viewMode) {
            'today' => $anchor->isToday(),
            'month' => $anchor->isSameMonth(now()),
            default => $anchor->copy()->startOfWeek()->isSameDay(now()->copy()->startOfWeek()),
        };
    }

    public function getCalendarDaysProperty(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return [];
        }

        $anchor = Carbon::parse($this->anchorDate);
        [$rangeStart, $rangeEnd] = $this->dateRange($anchor);

        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->whereBetween('date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->where('status', 'scheduled')
            ->get();
        $pendingRequests = CreditPurchaseRequest::query()
            ->where('requested_tenant_id', $tenant->id)
            ->whereBetween('requested_date', [$rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d')])
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->whereIn('payment_method', [CreditPurchaseRequest::METHOD_TRANSFER, CreditPurchaseRequest::METHOD_CASH])
            ->whereNotNull('requested_time_slot')
            ->get();

        $capacity = $tenant->capacity_per_slot ?? 5;
        $businessHours = collect($tenant->business_hours ?? []);
        $days = [];
        $cursor = $rangeStart->copy();

        while ($cursor->lte($rangeEnd)) {
            $days[] = $this->buildDay(
                $cursor->copy(),
                $appointments,
                $pendingRequests,
                $capacity,
                $businessHours,
                $anchor,
            );
            $cursor->addDay();
        }

        return $days;
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function dateRange(Carbon $anchor): array
    {
        return match ($this->viewMode) {
            'today' => [$anchor->copy(), $anchor->copy()],
            'month' => [
                $anchor->copy()->startOfMonth()->startOfWeek(),
                $anchor->copy()->endOfMonth()->endOfWeek(),
            ],
            default => [
                $anchor->copy()->startOfWeek(),
                $anchor->copy()->endOfWeek(),
            ],
        };
    }

    private function buildDay(
        Carbon $date,
        Collection $appointments,
        Collection $pendingRequests,
        int $capacity,
        Collection $businessHours,
        Carbon $monthAnchor,
    ): array {
        $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diasSemanaCorto = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        $dayOfWeek = $date->dayOfWeekIso;
        $dayConfig = $businessHours->firstWhere('day', $dayOfWeek);
        $slots = [];
        $foundNextForToday = false;

        if ($dayConfig && ! empty($dayConfig['slots'])) {
            $sortedSlots = collect($dayConfig['slots'])->sort()->values()->all();

            foreach ($sortedSlots as $timeString) {
                $slotTime = Carbon::parse($date->format('Y-m-d').' '.$timeString);
                $slotEndTime = $slotTime->copy()->addMinutes(59);

                $isPast = false;
                $isCurrent = false;
                $isNext = false;

                if ($date->isPast() && ! $date->isToday()) {
                    $isPast = true;
                } elseif ($date->isToday()) {
                    if (now()->greaterThan($slotEndTime)) {
                        $isPast = true;
                    } elseif (now()->between($slotTime, $slotEndTime)) {
                        $isCurrent = true;
                    } elseif (now()->lessThan($slotTime) && ! $foundNextForToday) {
                        $isNext = true;
                        $foundNextForToday = true;
                    }
                }

                $bookedCount = $appointments->filter(function ($app) use ($date, $timeString) {
                    return $app->date->format('Y-m-d') === $date->format('Y-m-d')
                        && Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                })->count();
                $heldByPendingRequests = $pendingRequests->filter(function (CreditPurchaseRequest $request) use ($date, $timeString) {
                    return Carbon::parse((string) $request->requested_date)->format('Y-m-d') === $date->format('Y-m-d')
                        && Carbon::parse((string) $request->requested_time_slot)->format('H:i') === $timeString;
                })->count();

                $available = $capacity - ($bookedCount + $heldByPendingRequests);

                if ($available >= 3) {
                    $color = 'emerald';
                } elseif ($available > 0) {
                    $color = 'amber';
                } else {
                    $color = 'red';
                }

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

        return [
            'date' => $date->format('d/m/Y'),
            'dayNumber' => $date->day,
            'dayName' => $diasSemana[$dayOfWeek],
            'dayNameShort' => $diasSemanaCorto[$dayOfWeek],
            'monthName' => ucfirst($date->translatedFormat('F')),
            'monthShort' => ucfirst($date->translatedFormat('M')),
            'year' => $date->year,
            'isToday' => $date->isToday(),
            'isOutsideMonth' => $this->viewMode === 'month' && $date->month !== $monthAnchor->month,
            'slots' => $slots,
        ];
    }
}
