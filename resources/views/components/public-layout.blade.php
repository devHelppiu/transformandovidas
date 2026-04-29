<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Transformando Vidas') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=urbanist:400,500,600,700,800|montserrat:400,500,600,700|fira-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-urbanist antialiased bg-white">

    <!-- ── Navbar ── -->
    <header x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 overflow-visible">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20 sm:h-24">

            <!-- Logo -->
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Transformando Vidas" class="h-14 sm:h-16 w-auto block">
            </a>

            <!-- Desktop nav -->
            <nav class="hidden sm:flex items-center gap-8">
                <a href="{{ route('consulta.tickets') }}"
                   class="font-montserrat text-sm font-medium text-gray-800 hover:text-tv-blue transition-colors">
                    Mis tickets
                </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="font-montserrat text-sm font-medium text-gray-800 hover:text-tv-blue transition-colors">
                        Mi panel
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="font-montserrat text-sm font-medium text-gray-500 hover:text-tv-blue transition-colors">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="font-montserrat text-sm font-medium text-gray-800 hover:text-tv-blue transition-colors">
                        Iniciar sesión
                    </a>
                @endauth
            </nav>

            <!-- Mobile burger -->
            <button @click="open = !open" class="sm:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-transition class="sm:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-3">
            <a href="{{ route('consulta.tickets') }}"
               class="block font-montserrat text-sm font-medium text-gray-800 py-2">Mis tickets</a>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="block font-montserrat text-sm font-medium text-gray-800 py-2">Mi panel</a>
            @else
                <a href="{{ route('login') }}"
                   class="block font-montserrat text-sm font-medium text-gray-800 py-2">Iniciar sesión</a>
            @endauth
        </div>
    </header>

    <!-- ── Content ── -->
    <main>
        {{ $slot }}
    </main>

    <!-- ── Footer ── -->
    <footer class="bg-tv-footer text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                <!-- Brand + Redes sociales -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Transformando Vidas" class="h-10 w-auto">
                    </div>
                    <p class="font-urbanist text-sm text-white/60 leading-relaxed max-w-xs mb-6">
                        Sorteos solidarios que cambian vidas.<br>
                        Cada ticket es una oportunidad real.
                    </p>
                    <!-- Redes sociales -->
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-white/70 hover:text-white transition-colors" aria-label="Facebook">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z"/></svg>
                        </a>
                        <a href="#" class="text-white/70 hover:text-white transition-colors" aria-label="Instagram">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 0 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>
                        </a>
                        <a href="#" class="text-white/70 hover:text-white transition-colors" aria-label="Twitter/X">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="text-white/70 hover:text-white transition-colors" aria-label="TikTok">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M16.6 5.82s.51.5 0 0A4.278 4.278 0 0 1 15.54 3h-3.09v12.4a2.592 2.592 0 0 1-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3s-1.88.09-3.24-1.48z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Autoriza (con logo Coljuegos) -->
                <div>
                    <h4 class="font-fira font-bold text-lg text-tv-bg mb-4">Autoriza</h4>
                    @if(file_exists(public_path('images/coljuegos-logo.png')))
                        <div class="bg-white rounded-lg p-2 inline-block mb-3">
                            <img src="{{ asset('images/coljuegos-logo.png') }}" alt="Coljuegos" class="h-8 w-auto">
                        </div>
                    @else
                        <p class="font-fira font-bold text-white text-base mb-2">Coljuegos</p>
                    @endif
                    <p class="font-fira text-sm text-white/70 leading-relaxed">
                        Operado bajo las normas vigentes de la República de Colombia.
                    </p>
                </div>

                <!-- Medios de pago -->
                <div>
                    <h4 class="font-fira font-bold text-lg text-tv-bg mb-4">Medios de pago</h4>
                    @if(file_exists(public_path('images/medios-pago.png')))
                        <img src="{{ asset('images/medios-pago.png') }}"
                             alt="Medios de pago: PSE, Daviplata, Nequi, Bre-B, Mastercard, Visa"
                             class="h-10 w-auto max-w-full"
                             loading="lazy">
                    @else
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-blue-800 text-sm">PSE</span></div>
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-orange-500 text-sm">Daviplata</span></div>
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-pink-600 text-sm">Nequi</span></div>
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-blue-600 text-sm">Bre-B</span></div>
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-red-600 text-sm">Mastercard</span></div>
                            <div class="bg-white rounded-lg px-3 py-2"><span class="font-bold text-blue-900 text-sm">Visa</span></div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="border-t border-white/10 mt-10 pt-6 text-center">
                <p class="font-urbanist text-xs text-white/60">
                    Copyright 2026 Transformando Vidas - Todos los derechos reservados
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
