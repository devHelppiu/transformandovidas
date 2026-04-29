<x-app-layout>
    <x-slot name="header">
        <h2 class="font-urbanist font-bold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ tab: 'sorteos' }">
            
            {{-- Total recaudado --}}
            <div class="bg-gradient-to-r from-tv-blue to-tv-pink rounded-2xl p-6 shadow-lg">
                <p class="font-urbanist text-white/80 text-sm">Total Recaudado (pagos verificados)</p>
                <p class="font-urbanist font-black text-white text-4xl">${{ number_format($totalRecaudado, 0, ',', '.') }}</p>
            </div>

            {{-- Chart de sorteos --}}
            @if($chartSorteos->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-urbanist font-bold text-gray-900 mb-4">Ventas por Sorteo</h3>
                <x-chart 
                    id="chartSorteos"
                    type="bar"
                    :labels="$chartSorteos->pluck('nombre')->toArray()"
                    :series="[
                        ['name' => 'Pagados', 'data' => $chartSorteos->pluck('pagados')->toArray()],
                        ['name' => 'Reservados', 'data' => $chartSorteos->pluck('reservados')->toArray()]
                    ]"
                    :colors="['#2227f5', '#e838bf']"
                    height="300"
                />
            </div>
            @endif

            {{-- Tab buttons --}}
            <div class="flex flex-wrap gap-2">
                <button @click="tab = 'sorteos'" 
                        :class="tab === 'sorteos' ? 'bg-tv-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl font-urbanist text-sm font-medium transition-colors">
                    Sorteos
                </button>
                <button @click="tab = 'coordinadores'" 
                        :class="tab === 'coordinadores' ? 'bg-tv-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl font-urbanist text-sm font-medium transition-colors">
                    Coordinadores
                </button>
                <button @click="tab = 'lideres'" 
                        :class="tab === 'lideres' ? 'bg-tv-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl font-urbanist text-sm font-medium transition-colors">
                    Líderes
                </button>
                <button @click="tab = 'comerciales'" 
                        :class="tab === 'comerciales' ? 'bg-tv-blue text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" 
                        class="px-4 py-2 rounded-xl font-urbanist text-sm font-medium transition-colors">
                    Comerciales
                </button>
            </div>

            {{-- Tab: Sorteos --}}
            <div x-show="tab === 'sorteos'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Sorteo</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Pagados</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Reservados</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Anulados</th>
                                <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sorteos as $sorteo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-urbanist font-semibold text-gray-900">{{ $sorteo->nombre }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $sorteo->tickets_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-green-600">{{ $sorteo->tickets_pagados_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-amber-600">{{ $sorteo->tickets_reservados_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-red-500">{{ $sorteo->tickets_anulados_count }}</td>
                                    <td class="px-6 py-4 text-center"><x-estado-badge :estado="$sorteo->estado" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Coordinadores --}}
            <div x-show="tab === 'coordinadores'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Coordinador</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Líderes</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comerciales</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets Rama</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comisión Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($ventasPorCoordinador as $coord)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <p class="font-urbanist font-semibold text-gray-900">{{ $coord->user?->name ?? 'Sin usuario' }}</p>
                                        <p class="font-urbanist text-xs text-gray-500">{{ $coord->user?->email }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $coord->lideres_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $coord->total_comerciales }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue">{{ $coord->tickets_rama }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-green-600">${{ number_format($coord->comision_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center font-urbanist text-gray-500">No hay coordinadores registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Líderes --}}
            <div x-show="tab === 'lideres'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Líder</th>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Coordinador</th>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comerciales</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets Directos</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets Comerciales</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comisión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($ventasPorLider as $lider)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-urbanist font-semibold text-gray-900">{{ $lider->user?->name ?? 'Sin usuario' }}</td>
                                    <td class="px-6 py-4 font-urbanist text-sm text-gray-500">{{ $lider->coordinador?->user?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-tv-blue">{{ $lider->codigo_ref }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $lider->comerciales_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $lider->tickets_directos }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist text-gray-600">{{ $lider->tickets_comerciales }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue">{{ $lider->total_tickets }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-green-600">${{ number_format($lider->comision_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center font-urbanist text-gray-500">No hay líderes registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab: Comerciales --}}
            <div x-show="tab === 'comerciales'" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Comercial</th>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Líder</th>
                                <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Comisión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($ventasPorComercial as $comercial)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-urbanist font-semibold text-gray-900">{{ $comercial->user?->name ?? 'Sin usuario' }}</td>
                                    <td class="px-6 py-4 font-urbanist text-sm text-gray-500">{{ $comercial->lider?->user?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-tv-blue">{{ $comercial->codigo_ref }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-tv-blue">{{ $comercial->tickets_referidos_count }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-gray-900">${{ number_format($comercial->monto_recaudado, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right font-urbanist font-bold text-green-600">${{ number_format($comercial->comision_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center font-urbanist text-gray-500">No hay comerciales registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>