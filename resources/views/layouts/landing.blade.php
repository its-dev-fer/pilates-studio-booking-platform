<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hannah Reforme Studio') }}</title>
    <meta name="description" content="Hannah Reforme Studio - Clases de pilates reformer, fuerza y movilidad con reserva en línea.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('assets/hannah_logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            color-scheme: dark;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 20% -10%, rgba(223, 232, 211, 0.14), transparent 35%),
                        radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.08), transparent 28%),
                        #020617;
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
            background-color: #e8dfd3;
            transition: all 0.3s ease-in-out;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .glass-nav {
            background-color: rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
</head>
<body
    class="antialiased text-white"
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

    <nav :class="{ 'py-3 glass-nav shadow-2xl border-white/15': scrolled, 'py-5 bg-transparent border-transparent': !scrolled }"
         class="fixed z-50 w-full border-b transition-all duration-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">

                <div class="relative z-50 shrink-0">
                    <a href="#" class="text-white text-3xl font-extrabold tracking-wider transition-transform duration-300 hover:scale-105 inline-block">
                        <img src="{{ asset('assets/hannah_logo.png') }}" alt="Hannah Reforme Studio" width="96" height="96" fetchpriority="high" class="h-auto w-24 invert-100">
                    </a>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-center space-x-8">
                        <a href="#hero" class="nav-link relative text-gray-200 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-300">Inicio</a>
                        <a href="#clases" class="nav-link relative text-gray-200 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-300">Clases</a>
                        <a href="#testimonios" class="nav-link relative text-gray-200 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-300">Testimonios</a>
                        <a href="#ubicacion" class="nav-link relative text-gray-200 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-300">Ubicación</a>
                        <a href="/comprar-creditos" class="nav-link relative text-gray-200 hover:text-white px-3 py-2 text-sm font-medium transition-colors duration-300">Comprar Creditos</a>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 rounded-full bg-primary/60 blur-sm opacity-50 transition duration-500 group-hover:opacity-100"></div>
                            <a href="/clientes/login" class="relative flex items-center rounded-full border border-white/25 bg-black/60 px-6 py-2.5 text-sm font-bold text-white transition-all duration-300 hover:border-primary hover:bg-primary hover:text-black">
                                Iniciar Sesión
                                <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="md:hidden z-50 relative flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex h-10 w-10 flex-col items-center justify-center text-white focus:outline-none" aria-label="Abrir menú">
                        <span :class="mobileMenuOpen ? 'rotate-45 translate-y-2.5 bg-primary' : 'bg-white'" class="block w-8 h-0.5 rounded-full transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? 'opacity-0 translate-x-3' : 'opacity-100'" class="block w-8 h-0.5 bg-white rounded-full mt-2 transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? '-rotate-45 -translate-y-2.5 bg-primary' : 'bg-white'" class="block w-8 h-0.5 rounded-full mt-2 transition-all duration-300 ease-in-out"></span>
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
         class="fixed inset-0 z-40 flex h-screen w-screen flex-col items-center justify-center bg-slate-950/95 backdrop-blur-xl">

        <div class="flex w-full flex-col items-center space-y-8 px-6">
            <a href="#hero" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white transition-colors duration-300 hover:text-primary">Inicio</a>
            <a href="#clases" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white transition-colors duration-300 hover:text-primary">Clases</a>
            <a href="#testimonios" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white transition-colors duration-300 hover:text-primary">Testimonios</a>
            <a href="#ubicacion" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white transition-colors duration-300 hover:text-primary">Ubicación</a>
            <a href="/comprar-creditos" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white transition-colors duration-300 hover:text-primary">Comprar Creditos</a>

            <div class="my-4 h-px w-full bg-white/20"></div>

            <a href="/clientes/login" class="w-full rounded-2xl bg-primary py-4 text-center text-xl font-bold text-black shadow-[0_0_20px_rgba(223,232,211,0.4)] transition">
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
