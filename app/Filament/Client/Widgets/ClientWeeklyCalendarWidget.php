<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use App\Models\CreditPurchaseRequest;
use App\Models\UserCredit;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ClientWeeklyCalendarWidget extends Widget
{
    protected string $view = 'filament.client.widgets.client-weekly-calendar-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public string $viewMode = 'week';

    public string $anchorDate;

    public ?array $modalDay = null;

    public function mount(): void
    {
        $this->anchorDate = now()->toDateString();
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('cliente') ?? false;
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

        if ($mode !== 'month') {
            $this->closeDayModal();
        }
    }

    public function goToToday(): void
    {
        $this->anchorDate = now()->toDateString();
        $this->closeDayModal();
    }

    public function previousPeriod(): void
    {
        $date = Carbon::parse($this->anchorDate);

        $this->anchorDate = match ($this->viewMode) {
            'month' => $date->subMonth()->toDateString(),
            'today' => $date->subDay()->toDateString(),
            default => $date->subWeek()->toDateString(),
        };

        $this->closeDayModal();
    }

    public function nextPeriod(): void
    {
        $date = Carbon::parse($this->anchorDate);

        $this->anchorDate = match ($this->viewMode) {
            'month' => $date->addMonth()->toDateString(),
            'today' => $date->addDay()->toDateString(),
            default => $date->addWeek()->toDateString(),
        };

        $this->closeDayModal();
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

        $user = auth()->user();
        $anchor = Carbon::parse($this->anchorDate);
        [$rangeStart, $rangeEnd] = $this->dateRange($anchor);

        $allAppointments = Appointment::where('tenant_id', $tenant->id)
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
                $allAppointments,
                $pendingRequests,
                $capacity,
                $businessHours,
                $anchor,
                $user,
            );
            $cursor->addDay();
        }

        return $days;
    }

    public function openDayModal(string $dateRaw): void
    {
        if ($this->viewMode !== 'month') {
            return;
        }

        $day = $this->buildDayForDate($dateRaw);
        if ($day) {
            $this->modalDay = $day;
        }
    }

    public function closeDayModal(): void
    {
        $this->modalDay = null;
    }

    private function buildDayForDate(string $dateRaw): ?array
    {
        $tenant = Filament::getTenant();
        $user = auth()->user();
        if (! $tenant || ! $user) {
            return null;
        }

        $date = Carbon::parse($dateRaw);
        $anchor = Carbon::parse($this->anchorDate);

        $allAppointments = Appointment::where('tenant_id', $tenant->id)
            ->whereDate('date', $dateRaw)
            ->where('status', 'scheduled')
            ->get();
        $pendingRequests = CreditPurchaseRequest::query()
            ->where('requested_tenant_id', $tenant->id)
            ->whereDate('requested_date', $dateRaw)
            ->where('status', CreditPurchaseRequest::STATUS_PENDING)
            ->whereIn('payment_method', [CreditPurchaseRequest::METHOD_TRANSFER, CreditPurchaseRequest::METHOD_CASH])
            ->whereNotNull('requested_time_slot')
            ->get();

        return $this->buildDay(
            $date,
            $allAppointments,
            $pendingRequests,
            $tenant->capacity_per_slot ?? 5,
            collect($tenant->business_hours ?? []),
            $anchor,
            $user,
        );
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
        Collection $allAppointments,
        Collection $pendingRequests,
        int $capacity,
        Collection $businessHours,
        Carbon $monthAnchor,
        $user,
    ): array {
        $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diasSemanaCorto = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        $dayOfWeek = $date->dayOfWeekIso;
        $dayConfig = $businessHours->firstWhere('day', $dayOfWeek);
        $slots = [];

        if ($dayConfig && ! empty($dayConfig['slots'])) {
            $sortedSlots = collect($dayConfig['slots'])->sort()->values()->all();

            foreach ($sortedSlots as $timeString) {
                $slotTime = Carbon::parse($date->format('Y-m-d').' '.$timeString);
                $isPast = ($date->isPast() && ! $date->isToday()) || ($date->isToday() && now()->greaterThan($slotTime));

                $bookedCount = $allAppointments->filter(function ($app) use ($date, $timeString) {
                    return $app->date->format('Y-m-d') === $date->format('Y-m-d')
                        && Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                })->count();
                $heldByPendingRequests = $pendingRequests->filter(function (CreditPurchaseRequest $request) use ($date, $timeString) {
                    return Carbon::parse((string) $request->requested_date)->format('Y-m-d') === $date->format('Y-m-d')
                        && Carbon::parse((string) $request->requested_time_slot)->format('H:i') === $timeString;
                })->count();

                $isBookedByMe = $allAppointments->contains(function ($app) use ($date, $timeString, $user) {
                    return $app->user_id === $user->id
                        && $app->date->format('Y-m-d') === $date->format('Y-m-d')
                        && Carbon::parse($app->time_slot)->format('H:i') === $timeString;
                });

                $available = $capacity - ($bookedCount + $heldByPendingRequests);

                if ($available >= 3) {
                    $color = 'emerald';
                } elseif ($available > 0) {
                    $color = 'amber';
                } else {
                    $color = 'red';
                }

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

        return [
            'date_raw' => $date->format('Y-m-d'),
            'date_formatted' => $date->format('d/m/Y'),
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

    public function bookClass($date, $time): void
    {
        $user = auth()->user();
        $tenant = Filament::getTenant();

        $exists = Appointment::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('time_slot', 'like', Carbon::parse($time)->format('H:i').'%')
            ->where('status', 'scheduled')
            ->exists();

        if ($exists) {
            Notification::make()->title('Ya estás inscrito en esta clase.')->warning()->send();

            return;
        }

        $activeCredit = UserCredit::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->where('balance', '>', 0)
            ->where('expires_at', '>', now())
            ->first();

        if (! $activeCredit) {
            Notification::make()
                ->title('Créditos Insuficientes')
                ->body('No tienes créditos activos en esta sucursal. Por favor adquiere un paquete para poder reservar.')
                ->warning()
                ->send();

            $this->redirect('/comprar-creditos');

            return;
        }

        $activeCredit->decrement('balance');

        Appointment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'date' => $date,
            'time_slot' => $time,
            'status' => 'scheduled',
            'check_in_status' => 'pendiente',
            'payment_method' => 'credit_balance',
            'booking_origin' => 'client_weekly_calendar',
        ]);

        Notification::make()
            ->title('¡Lugar Asegurado!')
            ->body('Hemos descontado 1 crédito. Nos vemos en clase.')
            ->success()
            ->send();

        if ($this->modalDay && $this->modalDay['date_raw'] === $date) {
            $this->modalDay = $this->buildDayForDate($date);
        }
    }
}
