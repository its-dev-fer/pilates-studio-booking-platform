@props([
    'basePrice',
    'finalPrice',
    'variant' => 'card',
])

@php
    $base = (float) $basePrice;
    $final = (float) $finalPrice;
    $discounted = abs($base - $final) > 0.009;
@endphp

@if ($variant === 'card')
    <div class="text-center">
        @if ($discounted)
            <div class="text-lg font-semibold text-stone-500 line-through">${{ number_format($base, 2) }} MXN</div>
            <span class="text-5xl font-black tracking-tight text-primary">${{ number_format($final, 2) }}</span>
            <span class="ml-1 text-base font-semibold text-stone-500">MXN</span>
        @else
            <span class="text-5xl font-black tracking-tight text-stone-900">${{ number_format($final, 2) }}</span>
            <span class="ml-1 text-base font-semibold text-stone-500">MXN</span>
        @endif
    </div>
@elseif ($variant === 'landing')
    @if ($discounted)
        <p class="text-sm text-stone-500 line-through">${{ number_format($base, 2) }} MXN</p>
        <p class="text-3xl font-black text-primary">${{ number_format($final, 2) }} MXN</p>
    @else
        <p class="text-3xl font-black text-primary">${{ number_format($final, 2) }} MXN</p>
    @endif
@elseif ($variant === 'compact')
    @if ($discounted)
        <span class="block text-xs text-stone-500 line-through">${{ number_format($base, 2) }} MXN</span>
        <span class="text-base font-bold text-stone-900">${{ number_format($final, 2) }} MXN</span>
    @else
        <span class="text-base font-bold text-stone-900">${{ number_format($final, 2) }} MXN</span>
    @endif
@elseif ($variant === 'table')
    @if ($discounted)
        <div class="flex flex-col gap-0.5">
            <span class="text-xs text-gray-500 line-through dark:text-gray-400">${{ number_format($base, 2) }}</span>
            <span class="text-sm font-semibold text-primary">${{ number_format($final, 2) }} MXN</span>
        </div>
    @else
        <span class="text-sm font-medium text-gray-950 dark:text-white">${{ number_format($final, 2) }} MXN</span>
    @endif
@elseif ($variant === 'infolist')
    @if ($discounted)
        <div class="space-y-1">
            <p class="text-sm text-gray-500 line-through dark:text-gray-400">${{ number_format($base, 2) }} MXN <span class="font-normal">(precio base)</span></p>
            <p class="text-base font-semibold text-primary">${{ number_format($final, 2) }} MXN <span class="font-normal text-gray-600 dark:text-gray-300">(importe actual)</span></p>
        </div>
    @else
        <p class="text-base font-semibold text-gray-950 dark:text-white">${{ number_format($final, 2) }} MXN</p>
    @endif
@endif
