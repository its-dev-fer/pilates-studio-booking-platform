@props(['day', 'compact' => false])

@forelse ($day['slots'] as $slot)
    @if ($slot['is_booked_by_me'])
        <div @class([
            'flex flex-col items-center justify-center rounded-xl border border-indigo-300 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:border-indigo-700 dark:text-indigo-300 shadow-sm relative transition-all',
            'py-3 px-2 mb-2 mx-1 scale-105 z-10' => ! $compact,
            'py-1 px-1 text-[9px] font-semibold' => $compact,
        ])>
            @unless ($compact)
                <span class="absolute -top-2.5 bg-indigo-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md tracking-wider">MI CLASE</span>
            @endunless
            <span @class(['font-bold mb-0.5', 'text-sm' => ! $compact, 'text-[10px]' => $compact])>{{ $slot['time_formatted'] }}</span>
            <span @class(['text-xs' => ! $compact])>{{ $compact ? 'Confirmado' : 'Lugar Confirmado' }}</span>
        </div>
    @elseif ($slot['is_past'])
        <div @class([
            'flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-gray-400 dark:bg-gray-800 dark:border-gray-700 opacity-50 cursor-not-allowed',
            'py-2 px-2 mb-2 mx-1' => ! $compact,
            'py-1 px-1 text-[9px]' => $compact,
        ])>
            <span @class(['mb-0.5', 'text-sm' => ! $compact, 'text-[10px]' => $compact])>{{ $slot['time_formatted'] }}</span>
            <span class="text-xs">Finalizada</span>
        </div>
    @elseif ($slot['available'] <= 0)
        <div @class([
            'flex flex-col items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-500 dark:bg-red-900/20 dark:border-red-800 cursor-not-allowed',
            'py-2 px-2 mb-2 mx-1' => ! $compact,
            'py-1 px-1 text-[9px] font-bold' => $compact,
        ])>
            <span @class(['mb-0.5', 'text-sm' => ! $compact, 'text-[10px]' => $compact])>{{ $slot['time_formatted'] }}</span>
            <span class="text-xs">¡Lleno!</span>
        </div>
    @else
        <button
            type="button"
            wire:click="bookClass('{{ $day['date_raw'] }}', '{{ $slot['time_raw'] }}')"
            wire:confirm="¿Seguro que deseas reservar la clase del {{ $day['dayName'] }} a las {{ $slot['time_formatted'] }}? Se descontará 1 crédito de tu paquete activo."
            @class([
                'w-full relative overflow-hidden flex flex-col items-center justify-center rounded-xl border text-center transition-all duration-200 hover:shadow-md cursor-pointer group',
                'py-3 px-2 mb-2 mx-1' => ! $compact,
                'py-1 px-1 text-[9px] font-semibold' => $compact,
                'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-400 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400' => $slot['color'] === 'emerald',
                'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100 hover:border-amber-400 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400' => $slot['color'] === 'amber',
            ])
        >
            <span @class(['font-semibold mb-0.5 transition-opacity duration-150 md:group-hover:opacity-0', 'text-sm' => ! $compact, 'text-[10px]' => $compact])>{{ $slot['time_formatted'] }}</span>
            <span @class(['transition-opacity duration-150 md:group-hover:opacity-0', 'text-xs' => ! $compact])>{{ $slot['available'] }} libres</span>
            @unless ($compact)
                <span class="pointer-events-none absolute inset-0 hidden items-center justify-center rounded-xl bg-black/10 text-sm font-bold tracking-wide opacity-0 transition-opacity duration-150 group-hover:opacity-100 md:flex">
                    ¡Reservar!
                </span>
            @endunless
        </button>
    @endif
@empty
    <div @class([
        'text-center text-gray-400 dark:text-gray-500 italic',
        'text-xs py-4' => ! $compact,
        'text-[9px] py-2' => $compact,
    ])>
        Sin clases disponibles.
    </div>
@endforelse
