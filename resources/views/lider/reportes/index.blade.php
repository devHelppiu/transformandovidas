<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Reportes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen de comisiones --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Total tickets vendidos</p>
                    <p class="font-urbanist font-bold text-2xl text-tv-blue mt-1">{{ number_format($totales['tickets_totales']) }}</p>
                    <p class="font-urbanist text-xs text-gray-400 mt-1">
                        {{ $totales['tickets_directos'] }} directos + {{ $totales['tickets_comerciales'] }} vía comerciales
                    </p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión acumulada</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($totales['comision_acumulada'], 0, ',', '.') }}</p>
                    <p class="font-urbanist text-xs text-gray-400 mt-1">
                        Directa: ${{ number_format($totales['comision_directa'], 0, ',', '.') }} | 
                        Override: ${{ number_format($totales['comision_override'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Estado de comisiones</p>
                    <div class="flex items-center gap-4 mt-1">
                        <div>
                            <p class="font-urbanist font-bold text-lg text-amber-600">${{ number_format($totales['comision_pendiente'], 0, ',', '.') }}</p>
                            <p class="font-urbanist text-xs text-gray-400">Pendiente</p>
                        </div>
                        <div>
                            <p class="font-urbanist font-bold text-lg text-green-600">${{ number_format($totales['comision_pagada'], 0, ',', '.') }}</p>
                            <p class="font-urbanist text-xs text-gray-400">Pagada</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventas por Comercial --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-urbanist font-bold text-gray-900">Ventas por Comercial</h3>
                </div>

                @if($comerciales->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">No tienes comerciales registrados.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Comercial</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets pagados</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($comerciales->sortByDesc('tickets_pagados') as $comercial)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-tv-bg flex items-center justify-center">
                                                    <span class="font-urbanist font-bold text-tv-blue text-sm">{{ substr($comercial->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-urbanist font-semibold text-gray-900">{{ $comercial->user->name }}</p>
                                                    <p class="font-urbanist text-xs text-gray-500">{{ $comercial->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-sm text-tv-blue">{{ $comercial->codigo_ref }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue-dark">{{ $comercial->tickets_pagados }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">${{ number_format($comercial->monto_recaudado, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Ventas por Sorteo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-urbanist font-bold text-gray-900">Ventas por Sorteo (mi equipo)</h3>
                </div>

                @if($sorteos->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">No hay sorteos registrados.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Sorteo</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Mis tickets</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comerciales</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($sorteos as $sorteo)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <p class="font-urbanist font-semibold text-gray-900">{{ $sorteo->nombre }}</p>
                                            <p class="font-urbanist text-xs text-gray-500">{{ $sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <x-estado-badge :estado="$sorteo->estado" />
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $sorteo->tickets_directos }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $sorteo->tickets_comerciales }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue-dark">{{ $sorteo->tickets_equipo }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">${{ number_format($sorteo->monto_equipo, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
