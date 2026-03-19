<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de Administración</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Sorteos Activos</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['sorteos_activos'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">de {{ $stats['total_sorteos'] }} totales</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Tickets Pagados</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['tickets_pagados'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">de {{ $stats['total_tickets'] }} totales</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Pagos Pendientes</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['pagos_pendientes'] }}</div>
                    <div class="text-xs text-gray-400 mt-1"><a href="{{ route('admin.pagos.index') }}" class="underline">Ver todos</a></div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Clientes / Comerciales</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_clientes'] }} / {{ $stats['total_comerciales'] }}</div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sorteo activo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Sorteo Activo</h3>
                    @if($sorteoActivo)
                        <p class="text-gray-900 font-medium">{{ $sorteoActivo->nombre }}</p>
                        <p class="text-sm text-gray-500">Fecha: {{ $sorteoActivo->fecha_sorteo->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-500">Tickets vendidos: {{ $sorteoActivo->ticketsVendidos() }} / {{ $sorteoActivo->total_tickets }}</p>
                        <a href="{{ route('admin.sorteos.show', $sorteoActivo) }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">Ver detalle &rarr;</a>
                    @else
                        <p class="text-gray-500">No hay sorteos activos.</p>
                        <a href="{{ route('admin.sorteos.create') }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">Crear sorteo &rarr;</a>
                    @endif
                </div>

                <!-- Últimos pagos pendientes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Últimos Pagos Pendientes</h3>
                    @forelse($ultimosPagos as $pago)
                        <div class="flex justify-between items-center py-2 border-b last:border-0">
                            <div>
                                <span class="text-sm font-medium">{{ $pago->ticket->comprador_nombre }}</span>
                                <span class="text-xs text-gray-500">- #{{ $pago->ticket->numero }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">${{ number_format($pago->monto, 0, ',', '.') }}</span>
                                <a href="{{ route('admin.pagos.show', $pago) }}" class="text-xs text-indigo-600 hover:underline">Revisar</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay pagos pendientes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>