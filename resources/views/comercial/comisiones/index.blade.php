<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Comisiones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Commission summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <p class="text-sm text-gray-500">Tipo de comisión</p>
                <p class="text-lg font-medium">
                    @if($comercial->comision_tipo)
                        {{ ucfirst($comercial->comision_tipo) }}:
                        {{ $comercial->comision_tipo === 'porcentaje' ? $comercial->comision_valor . '%' : '$' . number_format($comercial->comision_valor, 0, ',', '.') . ' por ticket' }}
                    @else
                        Sin configurar
                    @endif
                </p>
            </div>

            <!-- Commission table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sorteo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets Referidos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Recaudado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comisión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($comisiones as $comision)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium">{{ $comision->sorteo->nombre }}</td>
                                <td class="px-6 py-4 text-sm">{{ $comision->total_tickets_referidos }}</td>
                                <td class="px-6 py-4 text-sm">${{ number_format($comision->monto_recaudado, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600">${{ number_format($comision->monto_comision, 0, ',', '.') }}</td>
                                <td class="px-6 py-4"><x-estado-badge :estado="$comision->estado" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aún no tienes comisiones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $comisiones->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>