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

            <!-- Projected commissions (active raffles) -->
            @if($proyectadas->isNotEmpty())
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-amber-200">
                <div class="p-4 border-b border-amber-200 bg-amber-100/50">
                    <h3 class="font-semibold text-amber-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Comisiones Proyectadas (Sorteos Activos)
                    </h3>
                    <p class="text-sm text-amber-600 mt-1">Estas comisiones se liquidarán cuando finalice cada sorteo</p>
                </div>
                <table class="min-w-full divide-y divide-amber-200">
                    <thead class="bg-amber-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-700 uppercase">Sorteo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-700 uppercase">Tickets Referidos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-700 uppercase">Monto Recaudado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-700 uppercase">Comisión Estimada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-amber-700 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-amber-100">
                        @foreach($proyectadas as $proyectada)
                            <tr class="bg-amber-50/30">
                                <td class="px-6 py-4 text-sm font-medium">{{ $proyectada->sorteo->nombre }}</td>
                                <td class="px-6 py-4 text-sm">{{ $proyectada->total_tickets_referidos }}</td>
                                <td class="px-6 py-4 text-sm">${{ number_format($proyectada->monto_recaudado, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-amber-600">${{ number_format($proyectada->monto_comision, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Proyectada
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 bg-amber-50 text-xs text-amber-600 text-center">
                    * Las comisiones proyectadas pueden variar si se venden más tickets antes del cierre
                </div>
            </div>
            @endif

            <!-- Commission table (liquidated) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-700">Comisiones Liquidadas</h3>
                </div>
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
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aún no tienes comisiones liquidadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $comisiones->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>