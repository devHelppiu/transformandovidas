<x-app-layout>
    <x-slot name="header">
        <h2 class="font-urbanist font-bold text-xl text-gray-800 leading-tight">Panel de Administración</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            {{-- Stats Grid - Row 1: Sorteos y Tickets --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Sorteos Activos</p>
                    <p class="font-urbanist font-bold text-3xl text-tv-blue mt-1">{{ $stats['sorteos_activos'] }}</p>
                    <p class="font-urbanist text-xs text-gray-400 mt-1">de {{ $stats['total_sorteos'] }} totales</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Tickets Pagados</p>
                    <p class="font-urbanist font-bold text-3xl text-green-600 mt-1">{{ number_format($stats['tickets_pagados']) }}</p>
                    <p class="font-urbanist text-xs text-gray-400 mt-1">de {{ number_format($stats['total_tickets']) }} totales</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Pagos Pendientes</p>
                    <p class="font-urbanist font-bold text-3xl text-amber-600 mt-1">{{ $stats['pagos_pendientes'] }}</p>
                    <a href="{{ route('admin.pagos.index') }}" class="font-urbanist text-xs text-tv-blue hover:underline">Ver todos →</a>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Clientes / Comerciales</p>
                    <p class="font-urbanist font-bold text-3xl text-tv-blue-dark mt-1">
                        {{ $stats['total_clientes'] }} <span class="text-gray-300">/</span> {{ $stats['total_comerciales'] }}
                    </p>
                </div>
            </div>

            {{-- Stats Grid - Row 2: Jerarquía y Comisiones --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Coordinadores</p>
                    <p class="font-urbanist font-bold text-3xl text-purple-600 mt-1">{{ $stats['total_coordinadores'] }}</p>
                    <a href="{{ route('admin.coordinadores.index') }}" class="font-urbanist text-xs text-tv-blue hover:underline">Ver todos →</a>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Líderes</p>
                    <p class="font-urbanist font-bold text-3xl text-pink-600 mt-1">{{ $stats['total_lideres'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Comisiones Pendientes</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($stats['comisiones_pendientes'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Comisiones Totales</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">${{ number_format($stats['comisiones_acumuladas'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Sorteo activo --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-urbanist font-bold text-gray-900 mb-4">Sorteo Activo</h3>
                    @if($sorteoActivo)
                        <p class="font-urbanist font-semibold text-gray-900">{{ $sorteoActivo->nombre }}</p>
                        <p class="font-urbanist text-sm text-gray-500">Fecha: {{ $sorteoActivo->fecha_sorteo->format('d/m/Y') }}</p>
                        <div class="mt-3">
                            <div class="flex justify-between font-urbanist text-xs text-gray-500 mb-1">
                                <span>Progreso de ventas</span>
                                <span>{{ $sorteoActivo->ticketsVendidos() }} / {{ $sorteoActivo->total_tickets }}</span>
                            </div>
                            @php $pctVendido = $sorteoActivo->total_tickets > 0 ? round(($sorteoActivo->ticketsVendidos() / $sorteoActivo->total_tickets) * 100, 1) : 0; @endphp
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-tv-blue to-tv-pink" style="width: {{ max($pctVendido, 1) }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('admin.sorteos.show', $sorteoActivo) }}" class="font-urbanist mt-4 inline-flex items-center text-sm text-tv-blue hover:underline">Ver detalle →</a>
                    @else
                        <p class="font-urbanist text-gray-500">No hay sorteos activos.</p>
                        <a href="{{ route('admin.sorteos.create') }}" class="font-urbanist mt-3 inline-flex items-center text-sm text-tv-blue hover:underline">Crear sorteo →</a>
                    @endif
                </div>

                {{-- Últimos pagos pendientes --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-urbanist font-bold text-gray-900 mb-4">Últimos Pagos Pendientes</h3>
                    @forelse($ultimosPagos as $pago)
                        <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <span class="font-urbanist text-sm font-medium text-gray-900">{{ $pago->ticket->comprador_nombre }}</span>
                                <span class="font-mono text-xs text-gray-400">- #{{ $pago->ticket->numero }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-urbanist text-sm font-semibold text-gray-700">${{ number_format($pago->monto, 0, ',', '.') }}</span>
                                <a href="{{ route('admin.pagos.show', $pago) }}" class="font-urbanist text-xs text-tv-blue hover:underline">Revisar</a>
                            </div>
                        </div>
                    @empty
                        <p class="font-urbanist text-gray-500 text-sm">No hay pagos pendientes.</p>
                    @endforelse
                </div>
            </div>

            {{-- Acceso rápido a configuración --}}
            <div class="bg-gradient-to-r from-tv-blue to-tv-pink rounded-2xl p-6 shadow-lg">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="font-urbanist font-bold text-white text-lg">Configuración de Comisiones</h3>
                        <p class="font-urbanist text-white/80 text-sm">Ajusta los porcentajes de comisión por rol y canal</p>
                    </div>
                    <a href="{{ route('admin.comisiones.config') }}" 
                       class="bg-white text-tv-blue px-6 py-2.5 rounded-xl font-urbanist font-bold text-sm hover:bg-gray-50 transition-colors shadow-md">
                        Configurar →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>