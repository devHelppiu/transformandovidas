<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Tickets</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sorteo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-gray-900">{{ $ticket->numero }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->sorteo->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($ticket->tipo_asignacion) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-estado-badge :estado="$ticket->estado" /></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->pago)
                                        <x-estado-badge :estado="$ticket->pago->estado" />
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('cliente.tickets.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No tienes tickets.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>