<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hannah Reforme Studio') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Animación personalizada para el subrayado de los links en desktop */
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background-color: #e8dfd3; /* primary */
            transition: all 0.3s ease-in-out;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <nav :class="{ 'py-3 bg-black/80 backdrop-blur-lg shadow-2xl': scrolled, 'py-6 bg-transparent': !scrolled }"
         class="fixed w-full z-50 transition-all duration-500 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">

                <div class="flex-shrink-0 z-50 relative">
                    <a href="#" class="text-white text-3xl font-extrabold tracking-wider transition-transform duration-300 hover:scale-105 inline-block">
                        <img src="{{ asset('assets/hannah_logo.png') }}" alt="Hannah Reforme Studio" class="h-auto w-24 invert-100">
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
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-primary to-primary rounded-full blur opacity-50 group-hover:opacity-100 transition duration-500"></div>
                            <a href="/clientes/login" class="relative bg-black border border-white/20 text-white hover:text-black px-6 py-2.5 rounded-full text-sm font-bold transition-all duration-300 hover:bg-primary hover:border-primary flex items-center">
                                Iniciar Sesión
                                <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="md:hidden z-50 relative flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white focus:outline-none w-10 h-10 flex flex-col justify-center items-center group">
                        <span :class="mobileMenuOpen ? 'rotate-45 translate-y-2.5 bg-primary' : 'bg-white'" class="block w-8 h-0.5 rounded-full transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? 'opacity-0 translate-x-3' : 'opacity-100'" class="block w-8 h-0.5 bg-white rounded-full mt-2 transition-all duration-300 ease-in-out"></span>
                        <span :class="mobileMenuOpen ? '-rotate-45 -translate-y-2.5 bg-primary' : 'bg-white'" class="block w-8 h-0.5 rounded-full mt-2 transition-all duration-300 ease-in-out"></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div x-show="mobileMenuOpen"
         style="display: none;"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed inset-0 z-40 bg-black/95 backdrop-blur-2xl flex flex-col justify-center items-center h-screen w-screen">

        <div class="flex flex-col items-center space-y-8 w-full px-6">
            <a href="#hero" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white hover:text-primary transition-colors transform hover:scale-110 duration-300">Inicio</a>
            <a href="#clases" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white hover:text-primary transition-colors transform hover:scale-110 duration-300">Clases</a>
            <a href="#testimonios" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white hover:text-primary transition-colors transform hover:scale-110 duration-300">Testimonios</a>
            <a href="#ubicacion" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white hover:text-primary transition-colors transform hover:scale-110 duration-300">Ubicación</a>
            <a href="/comprar-creditos" @click="mobileMenuOpen = false" class="text-3xl font-bold text-white hover:text-primary transition-colors transform hover:scale-110 duration-300">Comprar Creditos</a>

            <div class="w-full h-px bg-white/20 my-4"></div>

            <a href="/clientes/login" class="w-full text-center bg-primary text-black py-4 rounded-2xl text-xl font-bold transition shadow-[0_0_20px_rgba(223,232,211,0.4)]">
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
