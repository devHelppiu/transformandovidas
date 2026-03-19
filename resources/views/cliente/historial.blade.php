<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Historial de Sorteos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @forelse($sorteosConTickets as $sorteo)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $sorteo->nombre }}</h3>
                            <p class="text-sm text-gray-500">{{ $sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                        </div>
                        <x-estado-badge :estado="$sorteo->estado" />
                    </div>

                    @if($sorteo->numero_ganador)
                        <div class="p-3 bg-purple-50 rounded-lg mb-4">
                            <p class="text-sm text-purple-800">
                                Número ganador: <span class="font-mono font-bold text-lg">{{ $sorteo->numero_ganador }}</span>
                            </p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        @foreach($sorteo->tickets as $ticket)
                            <div class="flex justify-between items-center p-3 rounded-lg {{ $sorteo->numero_ganador === $ticket->numero ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-bold text-lg">{{ $ticket->numero }}</span>
                                    @if($sorteo->numero_ganador === $ticket->numero)
                                        <span class="text-yellow-600 font-bold text-sm">&#127881; GANADOR</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-estado-badge :estado="$ticket->estado" />
                                    @if($ticket->pago)
                                        <x-estado-badge :estado="$ticket->pago->estado" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    No tienes participaciones en sorteos anteriores.
                </div>
            @endforelse

            <div class="mt-4">{{ $sorteosConTickets->links() }}</div>
        </div>
    </div>
</x-app-layout>