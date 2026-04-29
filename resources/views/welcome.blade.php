<x-public-layout>
    <x-slot name="title">{{ config('app.name') }} — Sorteos Solidarios</x-slot>

    {{-- ── HERO ── --}}
    <section class="relative bg-tv-blue overflow-hidden min-h-[520px] md:min-h-[620px] lg:min-h-[680px]">
        {{-- Imagen de fondo full-bleed --}}
        <div class="absolute inset-0 pointer-events-none">
            <img src="{{ asset('images/hero-banner.png') }}" 
                 alt="" 
                 class="w-full h-full object-cover object-center md:object-right">
            {{-- Degradé que protege el texto a la izquierda --}}
            <div class="absolute inset-0 bg-gradient-to-r from-tv-blue via-tv-blue/85 to-tv-blue/0 md:from-tv-blue md:via-tv-blue/70 md:to-transparent"></div>
            {{-- Vignette inferior para mejorar transición a la siguiente sección --}}
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-tv-blue/20 to-transparent"></div>
        </div>

        {{-- Blob decorativo rosa (sutil, encima del overlay) --}}
        <div class="absolute -top-32 -right-32 w-[420px] h-[420px] rounded-full bg-tv-pink/20 blur-3xl pointer-events-none mix-blend-screen"></div>

        {{-- Contenido del hero (texto a la izquierda, imagen como fondo) --}}
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 lg:py-32">
            <div class="max-w-xl md:max-w-2xl">
                <h1 class="font-urbanist font-bold text-white leading-tight"
                    style="font-size: clamp(2rem, 5vw, 3.2rem);">
                    Participa, gana y empieza el cambio<br class="hidden md:block"> que estás buscando.
                </h1>
                <p class="mt-5 font-urbanist font-normal text-white/90 text-base md:text-lg max-w-lg leading-relaxed">
                    Descubre los sorteos activos y elige la oportunidad que puede marcar un antes y un después en tu vida. ¡Hoy puede ser tu momento!
                </p>
                <a href="#sorteos"
                   class="inline-block mt-8 bg-tv-pink hover:bg-tv-pink/90 text-white font-urbanist font-bold text-sm md:text-base px-7 py-3.5 rounded-xl transition-all duration-200 shadow-lg shadow-tv-pink/30 active:scale-[0.98]">
                    Ver sorteos activos
                </a>
            </div>
        </div>
    </section>

    {{-- ── SORTEOS ACTIVOS ── --}}
    <section id="sorteos" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <h2 class="font-urbanist font-bold text-tv-blue-dark"
                    style="font-size: clamp(2rem, 4vw, 3rem);">
                    Sorteos activos
                </h2>
                <a href="#sorteos"
                   class="font-urbanist font-semibold text-tv-blue-dark text-sm hover:underline hidden sm:block">
                    Ver todos los sorteos →
                </a>
            </div>

            @if($sorteos->count())
                <div class="@if($sorteos->count() === 1) max-w-3xl mx-auto @else flex flex-wrap justify-center gap-6 @endif">
                    @foreach($sorteos as $sorteo)
                        @php
                            $vendidos = $sorteo->tickets()->whereIn('estado', ['reservado','pagado'])->count();
                            $pct = $sorteo->total_tickets > 0
                                ? round(($vendidos / $sorteo->total_tickets) * 100, 1)
                                : 0;
                        @endphp
                        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-tv-blue/20 transition-all duration-300 overflow-hidden flex flex-col @if($sorteos->count() > 1) w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] @endif">
                            {{-- Imagen del sorteo (si existe) --}}
                            @if($sorteo->imagen)
                                <div class="aspect-[16/9] w-full overflow-hidden bg-tv-bg">
                                    <img src="{{ asset('storage/' . $sorteo->imagen) }}"
                                         alt="{{ $sorteo->nombre }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         loading="lazy">
                                </div>
                            @endif

                            {{-- Fecha del sorteo --}}
                            <div class="px-6 pt-5 pb-3">
                                <p class="font-urbanist text-sm text-gray-500">
                                    Fecha del sorteo: {{ $sorteo->fecha_sorteo->format('d/m/Y') }}
                                </p>
                            </div>

                            {{-- Progress bar (relleno y label en tv-pink) --}}
                            <div class="px-6 pb-4">
                                <div class="flex justify-between text-xs font-urbanist text-gray-500 mb-1.5">
                                    <span class="font-semibold text-tv-pink">{{ $pct }}% Vendido</span>
                                    <span>{{ number_format($sorteo->total_tickets, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-tv-pink transition-all duration-500"
                                         style="width: {{ max($pct, 1) }}%"></div>
                                </div>
                            </div>

                            {{-- Título y descripción --}}
                            <div class="px-6 pb-4">
                                <p class="font-urbanist font-bold text-tv-blue-dark text-2xl uppercase leading-tight">
                                    {{ $sorteo->nombre }}
                                </p>
                                @if($sorteo->descripcion)
                                    <p class="font-urbanist text-sm text-gray-500 mt-2 line-clamp-2">
                                        {{ $sorteo->descripcion }}
                                    </p>
                                @endif
                            </div>

                            {{-- Precio ticket (más sutil, no compite con el premio) --}}
                            <div class="px-6 pb-3">
                                <span class="font-urbanist font-bold text-tv-blue text-lg">
                                    ${{ number_format($sorteo->precio_ticket, 0, ',', '.') }}
                                </span>
                                <span class="font-urbanist text-gray-500 text-sm ml-1">Valor del ticket</span>
                            </div>

                            {{-- Banda premio (fondo lavanda, premio dominante) --}}
                            <div class="bg-tv-bg px-6 py-5 mx-6 rounded-xl">
                                <p class="font-urbanist text-center leading-tight">
                                    <span class="block text-tv-blue-dark text-xs font-semibold uppercase tracking-wider mb-1">Premio</span>
                                    <span class="block text-tv-pink text-3xl font-black">${{ number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }}</span>
                                    @if(!empty($sorteo->premio_extra))
                                        <span class="block text-tv-pink/80 text-sm font-semibold mt-0.5">{{ $sorteo->premio_extra }}</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Botón comprar --}}
                            <div class="px-6 pt-4 pb-5">
                                <a href="{{ route('sorteo.publico', $sorteo) }}"
                                   class="flex items-center justify-between w-full bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm px-5 py-3 rounded-xl transition-all duration-200 active:scale-[0.98]">
                                    <span>Comprar</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="#sorteos"
                   class="block sm:hidden mt-6 text-center font-urbanist font-semibold text-tv-blue-dark text-sm hover:underline">
                    Ver todos los sorteos →
                </a>
            @else
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-tv-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-urbanist text-gray-500">Próximamente nuevos sorteos. ¡Mantente atento!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ── ¿CÓMO FUNCIONA? ── --}}
    <section class="bg-tv-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-urbanist font-bold text-tv-blue-dark text-center mb-14"
                style="font-size: clamp(2rem, 4vw, 3rem);">
                ¿Cómo funciona?
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $steps = [
                        ['num'=>'1','title'=>'Elige tu sorteo','desc'=>'Selecciona el sorteo activo y la cantidad de tickets que deseas comprar.'],
                        ['num'=>'2','title'=>'Completa tus datos','desc'=>'Ingresa tu nombre, correo y método de pago. No necesitas crear cuenta.'],
                        ['num'=>'3','title'=>'Paga seguro','desc'=>'Paga con Helppiu Pay: tarjetas, PSE, Nequi y más. Recibes confirmación al instante.'],
                        ['num'=>'4','title'=>'¡Gana!','desc'=>'Recibe tus números al azar y espera el sorteo. Si ganas, ¡te contactamos!'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    @if($index === 3)
                        {{-- Card #4 con fondo rosa --}}
                        <div class="bg-tv-pink rounded-2xl p-7 shadow-sm flex flex-col items-start gap-4">
                            <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <span class="font-urbanist font-bold text-tv-pink text-2xl">{{ $step['num'] }}</span>
                            </div>
                            <div>
                                <h3 class="font-urbanist font-bold text-white text-base mb-1">{{ $step['title'] }}</h3>
                                <p class="font-urbanist text-sm text-white/80 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl p-7 shadow-sm flex flex-col items-start gap-4">
                            <div class="w-14 h-14 bg-tv-blue rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <span class="font-urbanist font-bold text-white text-2xl">{{ $step['num'] }}</span>
                            </div>
                            <div>
                                <h3 class="font-urbanist font-bold text-gray-900 text-base mb-1">{{ $step['title'] }}</h3>
                                <p class="font-urbanist text-sm text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

</x-public-layout>
