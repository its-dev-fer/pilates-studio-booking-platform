<x-mail::message>
# ¡Hola, {{ $isAdmin ? 'Administradores' : $appointment->user->name }}!

{{ $isAdmin ? 'Se ha registrado una nueva reserva en el sistema.' : 'Tu lugar está asegurado. Hemos confirmado tu reserva para tu próxima clase.' }}

Aquí están los detalles de la cita:

<x-mail::panel>
**📅 Fecha:** {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}  
**⏰ Hora:** {{ \Carbon\Carbon::parse($appointment->time_slot)->format('h:i A') }}  
**📍 Sucursal:** {{ $appointment->tenant->name }}  
**👤 Cliente:** {{ $appointment->user->name }}
</x-mail::panel>

@if($isAdmin)
<x-mail::button :url="url('/dashboard')">
Ver en Panel de Administración
</x-mail::button>
@else
<x-mail::button :url="url('/clientes/login')">
Ver Mis Reservas
</x-mail::button>
@endif

Saludos,<br>
El sistema de {{ config('app.name') }}
</x-mail::message>