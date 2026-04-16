<x-mail::message>
# ¡Hola, {{ $user->name }}!

@if ($isPasswordReset ?? false)
Un administrador restableció tus credenciales de acceso. A continuación encontrarás tu nueva contraseña temporal.
@else
Bienvenido a nuestro portal. Tu cuenta ha sido creada exitosamente.
@endif

A continuación, te proporcionamos tus credenciales de acceso temporal:

**Correo:** {{ $user->email }}  
**Contraseña:** {{ $password }}

<x-mail::panel>
**⚠️ Importante:** Por tu seguridad, te solicitamos que cambies esta contraseña inmediatamente después de iniciar sesión por primera vez.
</x-mail::panel>

<x-mail::button :url="$loginUrl">
{{ ($isPasswordReset ?? false) ? 'Ingresar con la nueva contraseña' : 'Iniciar Sesión Ahora' }}
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>