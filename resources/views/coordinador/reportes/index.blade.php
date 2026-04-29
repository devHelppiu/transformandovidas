<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Reportes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Resumen de comisiones --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Tickets de mi rama</p>
                    <p class="font-urbanist font-bold text-2xl text-tv-blue mt-1">{{ number_format($totales['tickets_rama']) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión acumulada</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($totales['comision_acumulada'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión pendiente</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($totales['comision_pendiente'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión pagada</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">${{ number_format($totales['comision_pagada'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Ventas por Líder --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-urbanist font-bold text-gray-900">Ventas por Líder</h3>
                </div>

                @if($lideres->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">No tienes líderes registrados.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Líder</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets directos</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets comerciales</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Total tickets</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($lideres as $lider)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-tv-bg flex items-center justify-center">
                                                    <span class="font-urbanist font-bold text-tv-blue text-sm">{{ substr($lider->user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-urbanist font-semibold text-gray-900">{{ $lider->user->name }}</p>
                                                    <p class="font-mono text-xs text-gray-500">{{ $lider->codigo_ref }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $lider->tickets_directos }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $lider->tickets_comerciales }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue-dark">{{ $lider->total_tickets }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">${{ number_format($lider->monto_recaudado, 0, ',', '.') }}</td>
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
                    <h3 class="font-urbanist font-bold text-gray-900">Ventas por Sorteo (mi rama)</h3>
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
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets de mi rama</th>
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
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue-dark">{{ $sorteo->tickets_rama }}</td>
                                        <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">${{ number_format($sorteo->monto_rama, 0, ',', '.') }}</td>
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
