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
            class="overflow-x-auto pb-6 mt-4 snap-x snap-mandatory scroll-smooth -mx-4 px-4 sm:mx-0 sm:px-0"
            style="scrollbar-width: thin;"
        >
            <div class="flex lg:grid lg:grid-cols-7 gap-4 w-full">
                
                @foreach($this->weekData as $day)
                    <div class="flex-none w-[88%] sm:w-[45%] md:w-[32%] lg:w-auto flex flex-col border dark:border-gray-700 rounded-xl overflow-hidden snap-center transition-all duration-300 {{ $day['isToday'] ? 'ring-2 ring-primary-500 shadow-lg is-today-card' : '' }}">
                        
                        {{-- Cabecera del día --}}
                        <div class="py-2 text-center {{ $day['isToday'] ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                            <div class="font-bold text-sm uppercase">{{ $day['dayName'] }}</div>
                            <div class="text-xs opacity-80">{{ $day['date_formatted'] }}</div>
                        </div>

                        {{-- Lista de clases (Slots) --}}
                        <div class="p-2 space-y-2 bg-white dark:bg-gray-900 flex-grow relative">
                            
                            {{-- Loader mientras procesa la reserva --}}
                            <div wire:loading wire:target="bookClass" class="absolute inset-0 bg-white/70 dark:bg-black/70 z-50 flex items-center justify-center rounded-b-xl">
                                <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>

                            @forelse($day['slots'] as $slot)
                                @if($slot['is_booked_by_me'])
                                    {{-- CASO 1: YA ESTÁ INSCRITO (Azul/Morado, sin acción de click) --}}
                                    <div class="flex flex-col items-center justify-center py-3 px-2 mb-2 mx-1 rounded-xl border border-indigo-300 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:border-indigo-700 dark:text-indigo-300 shadow-sm relative scale-105 z-10 transition-all">
                                        <span class="absolute -top-2.5 bg-indigo-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md tracking-wider">MI CLASE</span>
                                        <span class="text-sm font-bold mb-0.5">{{ $slot['time_formatted'] }}</span>
                                        <span class="text-xs">Lugar Confirmado</span>
                                    </div>

                                @elseif($slot['is_past'])
                                    {{-- CASO 2: CLASE PASADA (Gris, opaca, sin click) --}}
                                    <div class="flex flex-col items-center justify-center py-2 px-2 mb-2 mx-1 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 dark:bg-gray-800 dark:border-gray-700 opacity-50 cursor-not-allowed">
                                        <span class="text-sm mb-0.5">{{ $slot['time_formatted'] }}</span>
                                        <span class="text-xs">Finalizada</span>
                                    </div>

                                @elseif($slot['available'] <= 0)
                                    {{-- CASO 3: CLASE LLENA (Roja, sin click) --}}
                                    <div class="flex flex-col items-center justify-center py-2 px-2 mb-2 mx-1 rounded-xl border border-red-200 bg-red-50 text-red-500 dark:bg-red-900/20 dark:border-red-800 cursor-not-allowed">
                                        <span class="text-sm mb-0.5">{{ $slot['time_formatted'] }}</span>
                                        <span class="text-xs font-bold">¡Lleno!</span>
                                    </div>

                                @else
                                    {{-- CASO 4: DISPONIBLE (Botón clickeable con confirmación) --}}
                                    <button 
                                        type="button"
                                        wire:click="bookClass('{{ $day['date_raw'] }}', '{{ $slot['time_raw'] }}')"
                                        wire:confirm="¿Seguro que deseas reservar la clase del {{ $day['dayName'] }} a las {{ $slot['time_formatted'] }}? Se descontará 1 crédito de tu paquete activo."
                                        class="w-full relative overflow-hidden flex flex-col items-center justify-center py-3 px-2 mb-2 mx-1 rounded-xl border text-center transition-all duration-200 hover:shadow-md cursor-pointer group
                                        {{ $slot['color'] === 'emerald' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-400 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400' : '' }}
                                        {{ $slot['color'] === 'amber' ? 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100 hover:border-amber-400 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400' : '' }}
                                    ">
                                        <span class="text-sm font-semibold mb-0.5 transition-opacity duration-150 md:group-hover:opacity-0">{{ $slot['time_formatted'] }}</span>
                                        <span class="text-xs transition-opacity duration-150 md:group-hover:opacity-0">{{ $slot['available'] }} libres</span>
                                        <span class="pointer-events-none absolute inset-0 hidden items-center justify-center rounded-xl bg-black/10 text-sm font-bold tracking-wide opacity-0 transition-opacity duration-150 group-hover:opacity-100 md:flex">
                                            ¡Reservar!
                                        </span>
                                    </button>
                                @endif
                            @empty
                                <div class="text-center text-xs text-gray-400 py-4 italic">
                                    Sin clases
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>