<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar-days class="w-6 h-6 text-primary-600" />
                <span>Calendario de Ocupación</span>
            </div>
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
                        <x-filament::button
                            wire:click="setViewMode('today')"
                            size="sm"
                            :color="$viewMode === 'today' ? 'primary' : 'gray'"
                        >
                            Día
                        </x-filament::button>
                        <x-filament::button
                            wire:click="setViewMode('week')"
                            size="sm"
                            :color="$viewMode === 'week' ? 'primary' : 'gray'"
                        >
                            Semana
                        </x-filament::button>
                        <x-filament::button
                            wire:click="setViewMode('month')"
                            size="sm"
                            :color="$viewMode === 'month' ? 'primary' : 'gray'"
                        >
                            Mes
                        </x-filament::button>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="font-medium text-gray-600 dark:text-gray-300">
                    {{ $this->todayReference }}
                </span>
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
                    <div class="text-center text-xs font-bold uppercase text-gray-500 dark:text-gray-400">
                        {{ $weekday }}
                    </div>
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
                        $displaySlots = $isCompact ? array_slice($day['slots'], 0, 3) : $day['slots'];
                        $hiddenSlotCount = count($day['slots']) - count($displaySlots);
                    @endphp

                    <div @class([
                        'flex flex-col border dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-300',
                        'snap-center' => $viewMode !== 'month',
                        'ring-2 ring-primary-500 shadow-lg is-today-card' => $day['isToday'],
                        'opacity-95 hover:opacity-100' => ! $day['isToday'] && ! ($day['isOutsideMonth'] ?? false),
                        'opacity-40' => $day['isOutsideMonth'] ?? false,
                        'flex-none w-[88%] sm:w-[45%] md:w-[32%] lg:w-auto' => $viewMode === 'week',
                        'w-full max-w-md' => $viewMode === 'today',
                    ])>
                        <div @class([
                            'text-center',
                            'px-3 py-3' => ! $isCompact,
                            'px-2 py-2' => $isCompact,
                            'bg-primary-500 text-white' => $day['isToday'],
                            'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' => ! $day['isToday'],
                        ])>
                            @if ($isCompact)
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
                            @else
                                <div class="flex items-center justify-center gap-3">
                                    <span class="text-4xl font-bold leading-none">{{ $day['dayNumber'] }}</span>
                                    <div class="text-left">
                                        <div class="text-sm font-bold uppercase tracking-wide">{{ $day['dayName'] }}</div>
                                        <div class="text-xs opacity-90">{{ $day['monthName'] }} {{ $day['year'] }}</div>
                                        @if ($day['isToday'])
                                            <div class="mt-1 inline-block rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide">
                                                Hoy
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div @class([
                            'p-2 space-y-2 bg-white dark:bg-gray-900 flex-grow',
                            'p-1 space-y-1' => $isCompact,
                        ])>
                            @forelse ($displaySlots as $slot)
                                <div @class([
                                    'flex flex-col items-center justify-center rounded-xl border text-center transition-all duration-300 relative',
                                    'py-3 px-2 mb-2 mx-1 text-xs font-semibold' => ! $isCompact,
                                    'py-1 px-1 text-[9px] font-semibold' => $isCompact,
                                    'opacity-40 hover:opacity-100 grayscale-[30%]' => $slot['is_past'] && ! $slot['is_current'],
                                    'scale-110 shadow-xl shadow-primary-500/30 ring-2 ring-primary-500 z-20 my-4' => $slot['is_current'] && ! $isCompact,
                                    'scale-105 shadow-lg shadow-blue-500/20 ring-2 ring-blue-400 z-10 my-3' => $slot['is_next'] && ! $isCompact,
                                    'hover:scale-105 hover:shadow-md hover:z-10' => ! $slot['is_past'] && ! $slot['is_current'] && ! $slot['is_next'] && ! $isCompact,
                                    'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400' => $slot['color'] === 'emerald',
                                    'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400' => $slot['color'] === 'amber',
                                    'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400' => $slot['color'] === 'red',
                                ])>
                                    @if ($slot['is_current'] && ! $isCompact)
                                        <span class="absolute -top-3 -right-2 bg-primary-600 text-white text-[9px] px-2 py-0.5 rounded-full animate-pulse shadow-md tracking-wider">EN CURSO</span>
                                    @endif

                                    @if ($slot['is_next'] && ! $isCompact)
                                        <span class="absolute -top-3 -right-2 bg-blue-500 text-white text-[9px] px-2 py-0.5 rounded-full shadow-md tracking-wider">PRÓXIMA</span>
                                    @endif

                                    <span @class(['mb-0.5', 'text-sm' => ! $isCompact, 'text-[10px]' => $isCompact])>{{ $slot['time'] }}</span>
                                    <span>
                                        @if ($slot['available'] <= 0)
                                            ¡Lleno!
                                        @else
                                            {{ $slot['available'] }}/{{ $slot['capacity'] }} libres
                                        @endif
                                    </span>
                                </div>
                            @empty
                                <div @class([
                                    'text-center text-gray-400 dark:text-gray-500 font-medium italic',
                                    'text-xs py-4' => ! $isCompact,
                                    'text-[9px] py-2' => $isCompact,
                                ])>
                                    Sin clases
                                </div>
                            @endforelse

                            @if ($hiddenSlotCount > 0)
                                <div class="text-center text-[9px] font-semibold text-gray-500 dark:text-gray-400">
                                    +{{ $hiddenSlotCount }} más
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
