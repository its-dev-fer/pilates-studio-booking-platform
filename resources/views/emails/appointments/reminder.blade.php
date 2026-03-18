<x-mail::message>
# ¡Hola, {{ $appointment->user->name }}!

Este es un recordatorio de que tu próxima clase en nuestra sucursal **{{ $appointment->tenant->name }}** está por comenzar.

<x-mail::panel>
**📅 Fecha:** {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}  
**⏰ Hora:** {{ \Carbon\Carbon::parse($appointment->time_slot)->format('h:i A') }}  
</x-mail::panel>

@if($type === '2h')
Te esperamos en 2 horas. Recuerda traer tu botella de agua y llegar con ropa cómoda.
@else
**¡Tu clase empieza en 15 minutos!** Ve preparándote, ¡nos vemos en el studio!
@endif

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>