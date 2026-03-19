<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tickets de {{ $email }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            @if($tickets->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 110 20 10 10 0 010-20z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 text-lg">No se encontraron tickets para este correo.</p>
                    <a href="{{ route('consulta.tickets') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800">Intentar con otro correo</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('ticket.detalle', $ticket) }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:ring-2 hover:ring-indigo-300 transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center border-2 border-indigo-200">
                                        <span class="text-xl font-mono font-bold text-indigo-700">{{ $ticket->numero }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $ticket->sorteo->nombre }}</p>
                                        <p class="text-sm text-gray-500">{{ $ticket->sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Comprado {{ $ticket->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <x-estado-badge :estado="$ticket->estado" />
                                    @if($ticket->pago)
                                        <p class="text-xs text-gray-500 mt-1">Pago: <x-estado-badge :estado="$ticket->pago->estado" /></p>
                                    @endif

                                    @if($ticket->sorteo->estado === 'ejecutado' && $ticket->sorteo->numero_ganador === $ticket->numero)
                                        <p class="text-sm font-bold text-yellow-600 mt-1">&#127881; ¡GANADOR!</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('consulta.tickets') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Buscar con otro correo</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
