<x-mail::message>
# ¡Hola, {{ $user->name }}!

Bienvenido a nuestro portal. Tu cuenta ha sido creada exitosamente.

A continuación, te proporcionamos tus credenciales de acceso temporal:

**Correo:** {{ $user->email }}  
**Contraseña:** {{ $password }}

<x-mail::panel>
**⚠️ Importante:** Por tu seguridad, te solicitamos que cambies esta contraseña inmediatamente después de iniciar sesión por primera vez.
</x-mail::panel>

<x-mail::button :url="$loginUrl">
Iniciar Sesión Ahora
</x-mail::button>

Saludos,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>