<x-mail::message>
# Hola, {{ $appointment->user->name }}.

Tu lugar esta asegurado. Hemos confirmado tu cita con los siguientes detalles:

<x-mail::panel>
**📅 Fecha:** {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}  
**⏰ Hora:** {{ \Carbon\Carbon::parse($appointment->time_slot)->format('h:i A') }}  
**📍 Sucursal:** {{ $appointment->tenant->name }}  
**👤 Cliente:** {{ $appointment->user->name }}  
**💳 Metodo de pago:** {{ $methodLabel }}  
**🧾 Origen del registro:** {{ $originLabel }}  
**📌 Solicitud de credito:** {{ $creditRequest ? 'Si' : 'No' }}
</x-mail::panel>

@if($creditRequest)
<x-mail::panel>
**🧩 Paquete solicitado:** {{ $creditRequest->package?->name ?? 'N/A' }}  
**💰 Monto cotizado:** ${{ number_format((float) ($creditRequest->quoted_final_price ?? 0), 2) }}  
**💳 Metodo en solicitud:** {{ $creditRequest->payment_method === 'transfer' ? 'Transferencia' : ($creditRequest->payment_method === 'cash' ? 'Efectivo' : 'N/A') }}  
**📅 Fecha de solicitud:** {{ $creditRequest->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
</x-mail::panel>
@endif

@if($appointment->payment_method === 'transfer')
<x-mail::panel>
**🏦 Cuenta bancaria para transferencia:** {{ $appointment->tenant?->transfer_account_number ?: 'No configurada. Contacta al estudio para validar la cuenta.' }}
</x-mail::panel>
@endif

<x-mail::button :url="url('/clientes/login')">
Ver Mis Reservas
</x-mail::button>

Saludos,<br>
El sistema de {{ config('app.name') }}
</x-mail::message>