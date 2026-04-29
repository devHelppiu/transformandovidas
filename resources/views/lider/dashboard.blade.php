<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Líder
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header con saludo --}}
            <div class="bg-gradient-to-r from-tv-blue to-tv-pink rounded-2xl p-6 text-white">
                <h1 class="font-urbanist font-bold text-2xl">Hola, {{ auth()->user()->name }}</h1>
                <p class="text-white/70 mt-1">
                    Coordinador: {{ $lider->coordinador->user->name ?? 'N/A' }} · 
                    {{ $totalComerciales }} {{ $totalComerciales === 1 ? 'Comercial' : 'Comerciales' }}
                </p>
            </div>

            {{-- Enlace de referido --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-urbanist font-bold text-gray-900">Tu enlace de referido</h3>
                    <span class="font-mono text-sm bg-tv-bg text-tv-blue px-3 py-1 rounded-lg">{{ $lider->codigo_ref }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $urlReferido }}" 
                           id="referidoUrl"
                           class="flex-1 rounded-lg border-gray-200 bg-gray-50 text-sm font-mono text-gray-600">
                    <button onclick="navigator.clipboard.writeText('{{ $urlReferido }}'); this.textContent = '¡Copiado!'; setTimeout(() => this.textContent = 'Copiar', 2000);"
                            class="px-4 py-2 bg-tv-blue text-white rounded-lg font-urbanist text-sm font-semibold hover:bg-tv-blue/90 transition">
                        Copiar
                    </button>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión acumulada</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($comisionesAcumuladas, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Pendiente por liquidar</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($comisionesPendientes, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión pagada</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">${{ number_format($comisionesPagadas, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Mis Comerciales --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-urbanist font-bold text-gray-900">Mis Comerciales</h3>
                    <a href="{{ route('lider.comerciales.create') }}" 
                       class="bg-tv-pink text-white px-4 py-2 rounded-lg font-urbanist text-sm font-semibold hover:bg-tv-pink/90 transition">
                        + Crear Comercial
                    </a>
                </div>
                
                @if($comerciales->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">Aún no tienes comerciales. ¡Crea el primero!</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Tickets</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Recaudado</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($comerciales as $comercial)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <p class="font-urbanist font-semibold text-gray-900">{{ $comercial->user->name }}</p>
                                            <p class="font-urbanist text-xs text-gray-500">{{ $comercial->user->email }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-sm text-gray-600">{{ $comercial->codigo_ref }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist font-semibold text-gray-900">
                                            {{ $comercial->tickets_vendidos }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-urbanist font-semibold text-gray-900">
                                            ${{ number_format($comercial->monto_recaudado, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($comercial->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-urbanist">Activo</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-urbanist">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('lider.comerciales.edit', $comercial) }}" 
                                               class="text-gray-400 hover:text-tv-blue transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Acciones rápidas --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('lider.comerciales.index') }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg font-urbanist text-sm text-gray-700 hover:border-tv-blue hover:text-tv-blue transition">
                    Ver todos los comerciales
                </a>
                <a href="{{ route('lider.comisiones.index') }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg font-urbanist text-sm text-gray-700 hover:border-tv-blue hover:text-tv-blue transition">
                    Ver mis comisiones
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
