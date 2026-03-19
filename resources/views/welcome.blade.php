<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Sorteos Solidarios</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Nav -->
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <a href="/" class="text-xl font-bold text-indigo-600">Transformando Vidas</a>
            <div class="flex items-center gap-4">
                <a href="{{ route('consulta.tickets') }}" class="text-sm text-gray-700 hover:text-indigo-600">🎟️ Mis Tickets</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-indigo-600">Mi Panel</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-indigo-600">Iniciar Sesión</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6">
                ¡Transforma tu vida<br>con un bono solidario!
            </h1>
            <p class="text-xl text-indigo-100 max-w-2xl mx-auto mb-8">
                Compra tu ticket, elige tu número de la suerte y participa en nuestros sorteos quincenales.
                Cada bono solidario es una oportunidad de ganar.
            </p>

            @if($sorteos->count())
                <div x-data="{
                    current: 0,
                    total: {{ $sorteos->count() }},
                    autoplay: null,
                    next() { this.current = (this.current + 1) % this.total },
                    prev() { this.current = (this.current - 1 + this.total) % this.total },
                    startAutoplay() { this.autoplay = setInterval(() => this.next(), 5000) },
                    stopAutoplay() { clearInterval(this.autoplay) },
                }" x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" class="relative max-w-2xl mx-auto">

                    {{-- Slides --}}
                    <div class="overflow-hidden">
                        @foreach($sorteos as $index => $sorteo)
                            @php
                                $vendidos = $sorteo->tickets()->whereIn('estado', ['reservado', 'pagado'])->count();
                                $pct = $sorteo->total_tickets > 0 ? round(($vendidos / $sorteo->total_tickets) * 100, 1) : 0;
                            @endphp
                            <div x-show="current === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-400"
                                 x-transition:enter-start="opacity-0 transform translate-x-8"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="flex flex-col items-center">

                                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-6 mb-6 w-full">
                                    <p class="text-indigo-200 text-sm mb-1">Sorteo activo</p>
                                    <p class="text-2xl font-bold">{{ $sorteo->nombre }}</p>
                                    <div class="flex gap-6 mt-3 justify-center text-sm">
                                        <div>
                                            <span class="text-indigo-200">Premio</span>
                                            <p class="text-xl font-bold">{{ '$' . number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-indigo-200">Ticket</span>
                                            <p class="text-xl font-bold">{{ '$' . number_format($sorteo->precio_ticket, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-indigo-200">Fecha</span>
                                            <p class="text-xl font-bold">{{ $sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress bar --}}
                                <div class="w-full max-w-md mb-6">
                                    <p class="text-lg font-bold text-yellow-300 mb-2">🔥 ¡Corre antes que se agoten!</p>
                                    <div class="relative w-full bg-white/20 rounded-full h-7 overflow-hidden">
                                        <div class="h-7 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-500" style="width: {{ max($pct, 2) }}%"></div>
                                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-white drop-shadow">{{ $pct }}%</span>
                                    </div>
                                </div>

                                <a href="{{ route('sorteo.publico', $sorteo) }}" class="bg-white text-indigo-700 px-8 py-3 rounded-lg text-lg font-bold hover:bg-indigo-50 transition">
                                    ¡Comprar Ticket Ahora!
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Navigation arrows --}}
                    @if($sorteos->count() > 1)
                        <button @click="prev()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 md:-translate-x-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full w-10 h-10 flex items-center justify-center transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="next()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 md:translate-x-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full w-10 h-10 flex items-center justify-center transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        {{-- Dots --}}
                        <div class="flex justify-center gap-2 mt-6">
                            @foreach($sorteos as $index => $sorteo)
                                <button @click="current = {{ $index }}" :class="current === {{ $index }} ? 'bg-white' : 'bg-white/40'" class="w-2.5 h-2.5 rounded-full transition"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <p class="text-indigo-200">Próximamente nuevos sorteos. ¡Mantente atento!</p>
            @endif
        </div>
    </div>

    <!-- How it works -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">¿Cómo funciona?</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-indigo-600">1</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Elige tu sorteo</h3>
                <p class="text-sm text-gray-600">Selecciona el sorteo activo y la cantidad de tickets que deseas comprar.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-indigo-600">2</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Completa tus datos</h3>
                <p class="text-sm text-gray-600">Ingresa tu nombre, correo y método de pago. No necesitas crear cuenta.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-indigo-600">3</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Paga tu bono</h3>
                <p class="text-sm text-gray-600">Realiza el pago por Nequi, Daviplata o Breb y sube tu comprobante.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-indigo-600">4</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">¡Gana!</h3>
                <p class="text-sm text-gray-600">Recibe tus números al azar y espera el sorteo. Si ganas, ¡te contactamos!</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p class="text-white font-semibold mb-2">Transformando Vidas</p>
            <p>&copy; {{ date('Y') }} Transformando Vidas. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
