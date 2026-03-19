<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hannah Reforme Studio') }} | Pilates Reformer y Reserva de Clases</title>
    <meta name="description" content="Hannah Reforme Studio: clases de pilates reformer, fuerza y movilidad en grupos reducidos. Reserva en línea y transforma tu cuerpo con coaches expertos.">
    <meta name="keywords" content="pilates reformer, clases de pilates, estudio de pilates, movilidad, fuerza funcional, reserva de clases, Hannah Reforme Studio">
    <meta name="author" content="Hannah Reforme Studio">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <meta name="googlebot" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <meta name="theme-color" content="#5e6b58">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="es-mx" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <meta property="og:locale" content="es_MX">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'Hannah Reforme Studio') }}">
    <meta property="og:title" content="{{ config('app.name', 'Hannah Reforme Studio') }} | Pilates Reformer y Reserva de Clases">
    <meta property="og:description" content="Reserva clases de pilates reformer, fuerza y movilidad en Hannah Reforme Studio. Entrena con técnica, seguridad y resultados.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('assets/hannah_logo.png') }}">
    <meta property="og:image:alt" content="Hannah Reforme Studio logo">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'Hannah Reforme Studio') }} | Pilates Reformer y Reserva de Clases">
    <meta name="twitter:description" content="Clases de pilates reformer y reserva en línea en Hannah Reforme Studio.">
    <meta name="twitter:image" content="{{ asset('assets/hannah_logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//videos.pexels.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('assets/hannah_logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            color-scheme: light;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 18% -8%, rgba(206, 185, 160, 0.22), transparent 38%),
                        radial-gradient(circle at 92% 0%, rgba(94, 107, 88, 0.06), transparent 32%),
                        rgb(255, 255, 253);
        }
        [x-cloak] {
            display: none !important;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background-color: #5e6b58;
            transition: all 0.3s ease-in-out;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .glass-nav {
            background-color: rgba(255, 255, 253, 0.82);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        main {
            content-visibility: auto;
            contain-intrinsic-size: 1px 1200px;
        }
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    <script type="application/ld+json">
        {
            "\u0040context": "https://schema.org",
            "\u0040type": "HealthClub",
            "name": "{{ config('app.name', 'Hannah Reforme Studio') }}",
            "url": "{{ config('app.url') }}",
            "image": "{{ asset('assets/hannah_logo.png') }}",
            "description": "Estudio de pilates reformer y entrenamiento funcional con reserva en línea.",
            "sameAs": [],
            "potentialAction": {
                "\u0040type": "ReserveAction",
                "target": "{{ url()->current() }}#reserva"
            }
        }
    </script>

    <script type="application/ld+json">
        {
            "\u0040context": "https://schema.org",
            "\u0040type": "WebSite",
            "name": "{{ config('app.name', 'Hannah Reforme Studio') }}",
            "url": "{{ config('app.url') }}",
            "inLanguage": "es-MX"
        }
    </script>
</head>
<body
    class="antialiased text-stone-800"
    x-data="{
        mobileMenuOpen: false,
        scrolled: false,
        ticking: false,
        handleScroll() {
            if (!this.ticking) {
                this.ticking = true;
                requestAnimationFrame(() => {
                    this.scrolled = window.scrollY > 36;
                    this.ticking = false;
                });
            }
        }
    }"
    x-init="window.addEventListener('scroll', () => handleScroll(), { passive: true }); handleScroll();"
>

    <nav :class="{ 'py-3 glass-nav shadow-lg shadow-stone-900/5 border-stone-200/80': scrolled, 'py-5 bg-transparent border-transparent': !scrolled }"
         class="fixed z-50 w-full border-b border-transparent transition-all duration-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">

                <div class="relative z-50 shrink-0">
                    <a href="/" aria-label="Ir a inicio" class="inline-block text-3xl font-extrabold tracking-wider transition-transform duration-300 hover:scale-105">
                        <img src="{{ asset('assets/hannah_logo.png') }}" alt="Hannah Reforme Studio" width="96" height="96" fetchpriority="high" decoding="async" class="h-auto w-24">
                    </a>
                </div>

                <div class="hidden lg:block">
                    <div class="ml-6 flex flex-wrap items-center justify-end gap-x-3 gap-y-2 xl:ml-10 xl:gap-x-4">
                        <a href="#hero" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Inicio</a>
                        <a href="#nosotros" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Nosotros</a>
                        <a href="#ubicaciones" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Ubicaciones</a>
                        <a href="#horarios" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Horarios</a>
                        <a href="#membresias" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Membresías</a>
                        <a href="#" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Tienda</a>
                        <a href="#contacto" class="nav-link relative px-2 py-2 text-xs font-medium text-stone-600 transition-colors duration-300 hover:text-primary xl:px-3 xl:text-sm">Contacto</a>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 rounded-full bg-primary/25 blur-sm opacity-60 transition duration-500 group-hover:opacity-100"></div>
                            <a href="/clientes/login" class="relative flex items-center rounded-full border border-stone-300 bg-white/90 px-6 py-2.5 text-sm font-bold text-primary shadow-sm transition-all duration-300 hover:border-primary hover:bg-primary hover:text-[rgb(255,255,253)]">
                                Iniciar Sesión
                                <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="lg:hidden z-50 relative flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex h-10 w-10 flex-col items-center justify-center text-stone-800 focus:outline-none" aria-label="Abrir menú">
                        <span :class="mobileMenuOpen ? 'rotate-45 translate-y-2.5 bg-primary' : 'bg-stone-700'" class="block w-8 h-0.5 rounded-full transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? 'opacity-0 translate-x-3' : 'opacity-100'" class="block mt-2 h-0.5 w-8 rounded-full bg-stone-700 transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? '-rotate-45 -translate-y-2.5 bg-primary' : 'bg-stone-700'" class="block w-8 h-0.5 rounded-full mt-2 transition-all duration-300 ease-in-out"></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div x-cloak x-show="mobileMenuOpen"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed inset-0 z-40 flex h-screen w-screen flex-col items-center justify-center bg-[rgb(255,255,253)]/95 backdrop-blur-xl">

        <div class="flex max-h-[min(100vh,720px)] w-full flex-col items-center space-y-5 overflow-y-auto px-6 py-8">
            <a href="#hero" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Inicio</a>
            <a href="#nosotros" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Nosotros</a>
            <a href="#ubicaciones" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Ubicaciones</a>
            <a href="#horarios" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Horarios</a>
            <a href="#membresias" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Membresías</a>
            <a href="#" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Tienda</a>
            <a href="#contacto" @click="mobileMenuOpen = false" class="text-2xl font-bold text-stone-800 transition-colors duration-300 hover:text-primary">Contacto</a>

            <div class="my-4 h-px w-full bg-stone-200"></div>

            <a href="/clientes/login" class="w-full rounded-2xl bg-primary py-4 text-center text-xl font-bold text-[rgb(255,255,253)] shadow-[0_12px_40px_-12px_rgba(94,107,88,0.35)] transition">
                Iniciar Sesión
            </a>
        </div>
    </div>

    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
