<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistema de Reservas') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        :root { color-scheme: light; }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 18% -8%, rgba(206, 185, 160, 0.18), transparent 38%),
                        radial-gradient(circle at 92% 0%, rgba(94, 107, 88, 0.05), transparent 32%),
                        rgb(255, 255, 253);
        }
    </style>
</head>
<body class="antialiased text-stone-800">

    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
