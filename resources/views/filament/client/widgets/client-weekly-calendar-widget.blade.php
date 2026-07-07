<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar class="w-6 h-6 text-primary-600" />
                <span>Calendario de Reservas</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Selecciona el horario que prefieras para apartar tu lugar.
        </x-slot>

        <div class="mb-4 space-y-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/60">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-filament::button wire:click="previousPeriod" size="sm" color="gray" icon="heroicon-o-chevron-left" />
                        <div class="min-w-[10rem] text-center">
                            <div @class([
                                'font-bold tracking-tight text-gray-900 dark:text-white',
                                'text-4xl leading-none' => $viewMode === 'today',
                                'text-2xl' => $viewMode !== 'today',
                            ])>
                                {{ $this->periodTitle }}
                            </div>
                            <div class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ $this->periodSubtitle }}
                            </div>
                        </div>
                        <x-filament::button wire:click="nextPeriod" size="sm" color="gray" icon="heroicon-o-chevron-right" />
                        <x-filament::button
                            wire:click="goToToday"
                            size="sm"
                            :color="$this->isViewingCurrentPeriod ? 'primary' : 'gray'"
                        >
                            Hoy
                        </x-filament::button>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-filament::button wire:click="setViewMode('today')" size="sm" :color="$viewMode === 'today' ? 'primary' : 'gray'">Día</x-filament::button>
                        <x-filament::button wire:click="setViewMode('week')" size="sm" :color="$viewMode === 'week' ? 'primary' : 'gray'">Semana</x-filament::button>
                        <x-filament::button wire:click="setViewMode('month')" size="sm" :color="$viewMode === 'month' ? 'primary' : 'gray'">Mes</x-filament::button>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $this->todayReference }}</span>
                @unless ($this->isViewingCurrentPeriod)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                        Viendo otra fecha
                    </span>
                @endunless
            </div>
        </div>

        @if ($viewMode === 'month')
            <div class="mb-2 grid grid-cols-7 gap-2">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $weekday)
                    <div class="text-center text-xs font-bold uppercase text-gray-500 dark:text-gray-400">{{ $weekday }}</div>
                @endforeach
            </div>
        @endif

        <div
            x-data
            x-init="
                setTimeout(() => {
                    let today = $el.querySelector('.is-today-card');
                    if (today && window.innerWidth < 1024 && @js($viewMode !== 'month')) {
                        today.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                }, 300)
            "
            @class([
                'pb-6 scroll-smooth',
                'overflow-x-auto snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0' => $viewMode !== 'month',
            ])
            style="scrollbar-width: thin;"
        >
            <div @class([
                'w-full',
                'grid grid-cols-7 gap-2' => $viewMode === 'month',
                'flex justify-center' => $viewMode === 'today',
                'flex lg:grid lg:grid-cols-7 gap-4' => $viewMode === 'week',
            ])>
                @foreach ($this->calendarDays as $day)
                    @php
                        $isCompact = $viewMode === 'month';
                        $bookableCount = collect($day['slots'])->filter(
                            fn (array $slot): bool => ! $slot['is_past'] && ! $slot['is_booked_by_me'] && $slot['available'] > 0
                        )->count();
                        $myClassesCount = collect($day['slots'])->where('is_booked_by_me', true)->count();
                    @endphp

                    @if ($isCompact)
                        <button
                            type="button"
                            wire:click="openDayModal('{{ $day['date_raw'] }}')"
                            @class([
                                'flex flex-col border dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-300 text-left w-full cursor-pointer hover:shadow-md hover:ring-1 hover:ring-primary-400/60',
                                'ring-2 ring-primary-500 shadow-lg is-today-card' => $day['isToday'],
                                'opacity-95' => ! $day['isToday'] && ! ($day['isOutsideMonth'] ?? false),
                                'opacity-40' => $day['isOutsideMonth'] ?? false,
                            ])
                        >
                            <div @class([
                                'px-2 py-2 text-center',
                                'bg-primary-500 text-white' => $day['isToday'],
                                'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' => ! $day['isToday'],
                            ])>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xl font-bold leading-none">{{ $day['dayNumber'] }}</span>
                                    @if ($day['isToday'])
                                        <span class="rounded-full bg-white/25 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wide">Hoy</span>
                                    @endif
                                </div>
                                <div class="mt-0.5 text-left text-[9px] font-semibold uppercase opacity-80">
                                    {{ $day['dayNameShort'] }}
                                    @if ($day['isOutsideMonth'] ?? false)
                                        · {{ $day['monthShort'] }}
                                    @endif
                                </div>
                            </div>

                            <div class="flex-grow bg-white p-2 dark:bg-gray-900">
                                @if (count($day['slots']) === 0)
                                    <p class="text-center text-[9px] italic text-gray-400">Sin clases</p>
                                @else
                                    <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-200">{{ count($day['slots']) }} horario(s)</p>
                                    @if ($myClassesCount > 0)
                                        <p class="mt-1 text-[9px] font-bold text-indigo-600 dark:text-indigo-400">{{ $myClassesCount }} tuya(s)</p>
                                    @endif
                                    @if ($bookableCount > 0)
                                        <p class="mt-1 text-[9px] font-semibold text-emerald-600 dark:text-emerald-400">{{ $bookableCount }} disponible(s)</p>
                                    @endif
                                    <p class="mt-2 text-[9px] font-bold uppercase tracking-wide text-primary-600 dark:text-primary-400">Ver clases</p>
                                @endif
                            </div>
                        </button>
                    @else
                        <div @class([
                            'flex flex-col border dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-300',
                            'snap-center',
                            'ring-2 ring-primary-500 shadow-lg is-today-card' => $day['isToday'],
                            'opacity-95 hover:opacity-100' => ! $day['isToday'],
                            'flex-none w-[88%] sm:w-[45%] md:w-[32%] lg:w-auto' => $viewMode === 'week',
                            'w-full max-w-md' => $viewMode === 'today',
                        ])>
                            <div @class([
                                'px-3 py-3 text-center',
                                'bg-primary-500 text-white' => $day['isToday'],
                                'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' => ! $day['isToday'],
                            ])>
                                <div class="flex items-center justify-center gap-3">
                                    <span class="text-4xl font-bold leading-none">{{ $day['dayNumber'] }}</span>
                                    <div class="text-left">
                                        <div class="text-sm font-bold uppercase tracking-wide">{{ $day['dayName'] }}</div>
                                        <div class="text-xs opacity-90">{{ $day['monthName'] }} {{ $day['year'] }}</div>
                                        @if ($day['isToday'])
                                            <div class="mt-1 inline-block rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide">Hoy</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex-grow space-y-2 bg-white p-2 dark:bg-gray-900">
                                <div wire:loading wire:target="bookClass" class="absolute inset-0 z-50 flex items-center justify-center rounded-b-xl bg-white/70 dark:bg-black/70">
                                    <svg class="h-6 w-6 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>

                                @include('filament.client.widgets.partials.calendar-day-slots', ['day' => $day, 'compact' => false])
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @if ($modalDay)
            <div
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                wire:keydown.escape.window="closeDayModal"
            >
                <div class="absolute inset-0 bg-gray-950/60" wire:click="closeDayModal"></div>

                <div class="relative flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-primary-600 dark:text-primary-400">Clases del día</p>
                                <h3 class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $modalDay['dayName'] }}, {{ $modalDay['date_formatted'] }}
                                </h3>
                            </div>
                            <button
                                type="button"
                                wire:click="closeDayModal"
                                class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                            >
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <div class="relative overflow-y-auto p-4">
                        <div wire:loading wire:target="bookClass" class="absolute inset-0 z-50 flex items-center justify-center bg-white/70 dark:bg-black/70">
                            <svg class="h-6 w-6 animate-spin text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>

                        @include('filament.client.widgets.partials.calendar-day-slots', ['day' => $modalDay, 'compact' => false])
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
