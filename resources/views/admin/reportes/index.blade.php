<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ tab: 'sorteos' }">
            <!-- Total -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <p class="text-sm text-gray-500">Total Recaudado (pagos verificados)</p>
                <p class="text-3xl font-bold text-green-600">${{ number_format($totalRecaudado, 0, ',', '.') }}</p>
            </div>

            <!-- Tab buttons -->
            <div class="flex gap-2 mb-6">
                <button @click="tab = 'sorteos'" :class="tab === 'sorteos' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'" class="px-4 py-2 rounded-md text-sm font-medium">
                    Ventas por Sorteo
                </button>
                <button @click="tab = 'comerciales'" :class="tab === 'comerciales' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'" class="px-4 py-2 rounded-md text-sm font-medium">
                    Ventas por Comercial
                </button>
            </div>

            <!-- Ventas por Sorteo -->
            <div x-show="tab === 'sorteos'">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sorteo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Tickets</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reservados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anulados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sorteos as $sorteo)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $sorteo->nombre }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $sorteo->tickets_count }}</td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $sorteo->tickets_pagados_count }}</td>
                                    <td class="px-6 py-4 text-sm text-yellow-600">{{ $sorteo->tickets_reservados_count }}</td>
                                    <td class="px-6 py-4 text-sm text-red-600">{{ $sorteo->tickets_anulados_count }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$sorteo->estado" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ventas por Comercial -->
            <div x-show="tab === 'comerciales'" x-cloak>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comercial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets Referidos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Recaudado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ventasPorComercial as $comercial)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $comercial->user->name }}</td>
                                    <td class="px-6 py-4 text-sm font-mono text-indigo-600">{{ $comercial->codigo_ref }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $comercial->tickets_referidos_count }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-green-600">${{ number_format($comercial->monto_recaudado, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>