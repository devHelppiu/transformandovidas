<x-public-layout>
    <x-slot name="title">Mis Tickets — {{ config('app.name') }}</x-slot>

    <div class="bg-tv-bg min-h-screen py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-flash-messages />

            <div class="mb-8 flex items-center justify-between">
                <h1 class="font-urbanist font-bold text-tv-blue-dark text-2xl md:text-3xl">
                    Mis Tickets
                </h1>
                <a href="{{ route('consulta.tickets') }}"
                   class="font-urbanist text-sm text-gray-500 hover:text-tv-blue transition-colors">
                    ← Buscar con otro correo
                </a>
            </div>

            @if($tickets->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-tv-bg rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 110 20 10 10 0 010-20z"/>
                        </svg>
                    </div>
                    <p class="font-urbanist font-semibold text-gray-700 text-lg">No se encontraron tickets</p>
                    <p class="font-urbanist text-sm text-gray-400 mt-1">para el correo <strong>{{ $email }}</strong></p>
                    <a href="{{ route('consulta.tickets') }}"
                       class="mt-5 inline-block font-urbanist text-sm font-semibold text-tv-blue hover:underline">
                        Intentar con otro correo
                    </a>
                </div>

            @else
                <div class="space-y-3">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('ticket.detalle', $ticket) }}"
                           class="flex items-center justify-between bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-tv-blue/30 hover:shadow-md transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-tv-bg rounded-xl flex items-center justify-center border-2 border-tv-blue/20 flex-shrink-0 group-hover:border-tv-blue/40 transition-colors">
                                    <span class="font-urbanist font-black text-tv-blue text-lg">{{ $ticket->numero }}</span>
                                </div>
                                <div>
                                    <p class="font-urbanist font-bold text-gray-900">{{ $ticket->sorteo->nombre }}</p>
                                    <p class="font-urbanist text-sm text-gray-500">{{ $ticket->sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                                    <p class="font-urbanist text-xs text-gray-400 mt-0.5">
                                        Comprado {{ $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <x-estado-badge :estado="$ticket->estado" />
                                @if($ticket->pago)
                                    <p class="font-urbanist text-xs text-gray-400 mt-1">
                                        Pago: <x-estado-badge :estado="$ticket->pago->estado" />
                                    </p>
                                @endif
                                @if($ticket->sorteo->estado === 'ejecutado' && $ticket->sorteo->numero_ganador === $ticket->numero)
                                    <p class="font-urbanist text-sm font-bold text-yellow-600 mt-1">🎉 ¡GANADOR!</p>
                                @endif
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-tv-blue mt-2 ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-public-layout>
