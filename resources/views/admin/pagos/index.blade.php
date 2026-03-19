<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Pagos</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sorteo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comprobante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pagos as $pago)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $pago->ticket->comprador_nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pago->ticket->sorteo->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold">{{ $pago->ticket->numero }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($pago->metodo) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($pago->monto, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($pago->comprobante_url)
                                        <a href="{{ route('admin.ticket.comprobante.ver', $pago->ticket) }}" target="_blank" class="text-indigo-600 hover:underline">Ver</a>
                                    @else
                                        <span class="text-gray-400">Sin comprobante</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-estado-badge :estado="$pago->estado" /></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    @if($pago->estado === 'pendiente')
                                        <a href="{{ route('admin.pagos.show', $pago) }}" class="text-indigo-600 hover:text-indigo-900">Revisar</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay pagos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $pagos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>