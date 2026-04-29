<x-cliente-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <!-- Active sorteo -->
            @if($sorteoActivo)
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 text-white">
                    <h3 class="text-2xl font-bold mb-2">{{ $sorteoActivo->nombre }}</h3>
                    <p class="text-indigo-100 mb-4">{{ $sorteoActivo->descripcion }}</p>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <span class="text-indigo-200 text-sm">Fecha del sorteo</span>
                            <p class="font-bold">{{ $sorteoActivo->fecha_sorteo->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <span class="text-indigo-200 text-sm">Precio del ticket</span>
                            <p class="font-bold">${{ number_format($sorteoActivo->precio_ticket, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-indigo-200 text-sm">Premio</span>
                            <p class="font-bold">${{ number_format($sorteoActivo->valor_premio ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('sorteo.publico', $sorteoActivo) }}" class="inline-flex items-center bg-white text-indigo-600 px-6 py-2 rounded-md text-sm font-bold hover:bg-indigo-50">
                        ¡Comprar Ticket!
                    </a>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                    <p class="text-gray-500 text-center">No hay sorteos activos en este momento.</p>
                </div>
            @endif

            <!-- My tickets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Mis Tickets</h3>
                    <a href="{{ route('cliente.tickets.index') }}" class="text-sm text-indigo-600 hover:underline">Ver todos</a>
                </div>
                @forelse($misTickets as $ticket)
                    <div class="p-4 border-b last:border-0 flex justify-between items-center hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-mono font-bold text-gray-800">{{ $ticket->numero }}</span>
                            <div>
                                <p class="text-sm font-medium">{{ $ticket->sorteo->nombre }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-estado-badge :estado="$ticket->estado" />
                            @if($ticket->pago)
                                <x-estado-badge :estado="$ticket->pago->estado" />
                            @endif
                            <a href="{{ route('cliente.tickets.show', $ticket) }}" class="text-sm text-indigo-600 hover:underline">Ver</a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No tienes tickets aún.
                        @if($sorteoActivo)
                            <a href="{{ route('sorteo.publico', $sorteoActivo) }}" class="text-indigo-600 hover:underline">¡Compra uno!</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-cliente-layout>