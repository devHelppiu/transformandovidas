<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Comisiones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Total acumulado</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($totales['acumulado'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Pendiente</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($totales['pendiente'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Pagado</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">${{ number_format($totales['pagado'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Lista de comisiones --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-urbanist font-bold text-gray-900">Historial de comisiones</h3>
                </div>

                @if($comisiones->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">No tienes comisiones registradas aún.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Sorteo</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comisión</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($comisiones as $comision)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-urbanist font-semibold text-gray-900">
                                            {{ $comision->sorteo->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">
                                            {{ $comision->total_tickets_referidos }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">
                                            ${{ number_format($comision->monto_recaudado, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">
                                            ${{ number_format($comision->monto_comision, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($comision->estado === 'pagada')
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-urbanist">Pagada</span>
                                            @else
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-urbanist">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $comisiones->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
