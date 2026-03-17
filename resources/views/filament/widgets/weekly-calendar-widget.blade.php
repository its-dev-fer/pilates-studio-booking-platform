<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calendar-days class="w-6 h-6 text-primary-600" />
                <span>Ocupación de la Semana</span>
            </div>
        </x-slot>

        {{-- 
            1. Alpine.js detecta la carga y hace scroll al "Día de Hoy" si estás en móvil
            2. snap-x y snap-mandatory crean el efecto "Carrusel"
        --}}
        <div 
            x-data 
            x-init="
                setTimeout(() => {
                    let today = $el.querySelector('.is-today-card');
                    if (today && window.innerWidth < 1024) {
                        today.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                }, 300)
            "
            class="overflow-x-auto pb-6 snap-x snap-mandatory scroll-smooth -mx-4 px-4 sm:mx-0 sm:px-0"
            style="scrollbar-width: thin;"
        >
            {{-- Flex para móvil/tablet, Grid de 7 columnas para Desktop (lg) --}}
            <div class="flex lg:grid lg:grid-cols-7 gap-4 w-full">
                
                @foreach($this->weekData as $day)
                    {{-- 
                        Responsividad:
                        - Móvil: w-[88%] (Toma casi toda la pantalla, dejando asomar el 12% del siguiente día para invitar a deslizar).
                        - Tablet: w-[45%] o w-[32%] (Muestra 2 o 3 días a la vez).
                        - PC: lg:w-auto (El Grid toma el control y los acomoda perfecto).
                        - snap-center: Fuerza a que el día quede centrado al terminar de deslizar.
                    --}}
                    <div class="
                        flex-none w-[88%] sm:w-[45%] md:w-[32%] lg:w-auto 
                        flex flex-col border dark:border-gray-700 rounded-xl overflow-hidden 
                        snap-center transition-all duration-300
                        {{ $day['isToday'] ? 'ring-2 ring-primary-500 shadow-lg is-today-card' : 'opacity-95 hover:opacity-100' }}
                    ">
                        
                        {{-- Cabecera del día --}}
                        <div class="py-2 text-center {{ $day['isToday'] ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                            <div class="font-bold text-sm uppercase">{{ $day['dayName'] }}</div>
                            <div class="text-xs opacity-80">{{ $day['date'] }}</div>
                        </div>

                        {{-- Lista de clases (Slots) --}}
                        <div class="p-2 space-y-2 bg-white dark:bg-gray-900 flex-grow">
                            @forelse($day['slots'] as $slot)
                                <div @class([
                                    'flex flex-col items-center justify-center py-3 px-2 mb-2 mx-1 rounded-xl border text-xs font-semibold text-center transition-all duration-300 relative',
                                    'opacity-40 hover:opacity-100 grayscale-[30%]' => $slot['is_past'] && !$slot['is_current'],
                                    'scale-110 shadow-xl shadow-primary-500/30 ring-2 ring-primary-500 z-20 my-4' => $slot['is_current'],
                                    'scale-105 shadow-lg shadow-blue-500/20 ring-2 ring-blue-400 z-10 my-3' => $slot['is_next'],
                                    'hover:scale-105 hover:shadow-md hover:z-10' => !$slot['is_past'] && !$slot['is_current'] && !$slot['is_next'],
                                    'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400' => $slot['color'] === 'emerald',
                                    'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400' => $slot['color'] === 'amber',
                                    'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400' => $slot['color'] === 'red',
                                ])>
                                    
                                    @if($slot['is_current'])
                                        <span class="absolute -top-3 -right-2 bg-primary-600 text-white text-[9px] px-2 py-0.5 rounded-full animate-pulse shadow-md tracking-wider">EN CURSO</span>
                                    @endif

                                    @if($slot['is_next'])
                                        <span class="absolute -top-3 -right-2 bg-blue-500 text-white text-[9px] px-2 py-0.5 rounded-full shadow-md tracking-wider">PRÓXIMA</span>
                                    @endif

                                    <span class="text-sm mb-0.5">{{ $slot['time'] }}</span>
                                    <span>
                                        @if($slot['available'] <= 0)
                                            ¡Lleno!
                                        @else
                                            {{ $slot['available'] }}/{{ $slot['capacity'] }} libres
                                        @endif
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-xs text-gray-400 dark:text-gray-500 py-4 font-medium italic">
                                    Sin clases programadas
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>