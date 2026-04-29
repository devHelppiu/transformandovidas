<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración de Comisiones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            {{-- Selector de sorteo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <form method="GET" action="{{ route('admin.comisiones.config') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <x-input-label for="sorteo" value="Configurar para sorteo específico (opcional)" />
                        <select id="sorteo" name="sorteo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-tv-blue focus:border-tv-blue font-urbanist">
                            <option value="">— Configuración Global (aplica a todos) —</option>
                            @foreach($sorteos as $sorteo)
                                <option value="{{ $sorteo->id }}" {{ $sorteoFiltro?->id == $sorteo->id ? 'selected' : '' }}>
                                    {{ $sorteo->nombre }} ({{ $sorteo->fecha_sorteo->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-urbanist font-medium text-sm px-5 py-2.5 rounded-xl transition-colors">
                        Cambiar
                    </button>
                </form>
            </div>

            {{-- Tabla de configuración --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-urbanist font-bold text-gray-900">
                            @if($sorteoFiltro)
                                Comisiones para: {{ $sorteoFiltro->nombre }}
                            @else
                                Configuración Global de Comisiones
                            @endif
                        </h3>
                        <p class="font-urbanist text-sm text-gray-500 mt-1">
                            @if($sorteoFiltro)
                                Estas configuraciones sobrescriben las globales solo para este sorteo.
                            @else
                                Estas configuraciones aplican a todos los sorteos que no tengan configuración específica.
                            @endif
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.comisiones.config.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="sorteo_id" value="{{ $sorteoFiltro?->id }}">

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Rol / Canal</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Valor</th>
                                    @if($sorteoFiltro)
                                        <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Valor Global</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($combinaciones as $i => $combo)
                                    @php
                                        $key = "{$combo['rol']}-{$combo['canal']}";
                                        $config = $sorteoFiltro ? ($especificas[$key] ?? null) : ($globales[$key] ?? null);
                                        $globalConfig = $globales[$key] ?? null;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <input type="hidden" name="configs[{{ $i }}][rol]" value="{{ $combo['rol'] }}">
                                            <input type="hidden" name="configs[{{ $i }}][canal]" value="{{ $combo['canal'] }}">
                                            <p class="font-urbanist font-semibold text-gray-900">{{ $combo['label'] }}</p>
                                            <p class="font-urbanist text-xs text-gray-500">{{ ucfirst($combo['rol']) }} - {{ $combo['canal'] }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <select name="configs[{{ $i }}][tipo]" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-tv-blue focus:border-tv-blue font-urbanist text-sm">
                                                <option value="porcentaje" {{ ($config?->tipo ?? 'porcentaje') === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                                                <option value="fijo" {{ ($config?->tipo ?? '') === 'fijo' ? 'selected' : '' }}>Monto fijo ($)</option>
                                                <option value="meta" {{ ($config?->tipo ?? '') === 'meta' ? 'selected' : '' }}>Por meta</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   name="configs[{{ $i }}][valor]" 
                                                   value="{{ $config?->valor ?? $globalConfig?->valor ?? 0 }}"
                                                   step="0.01"
                                                   min="0"
                                                   class="block w-32 border-gray-300 rounded-md shadow-sm focus:ring-tv-blue focus:border-tv-blue font-urbanist text-sm">
                                        </td>
                                        @if($sorteoFiltro)
                                            <td class="px-6 py-4">
                                                @if($globalConfig)
                                                    <span class="font-urbanist text-sm text-gray-500">
                                                        {{ $globalConfig->valor }}{{ $globalConfig->tipo === 'porcentaje' ? '%' : ' COP' }}
                                                    </span>
                                                @else
                                                    <span class="font-urbanist text-sm text-gray-400">No definido</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                        <p class="font-urbanist text-sm text-gray-500">
                            <svg class="w-4 h-4 inline-block mr-1 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Los cambios se aplican inmediatamente para nuevas ventas.
                        </p>
                        <button type="submit" class="bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm px-6 py-2.5 rounded-xl transition-colors">
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>

            {{-- Explicación --}}
            <div class="bg-tv-bg rounded-2xl p-6">
                <h4 class="font-urbanist font-bold text-gray-900 mb-3">¿Cómo funcionan las comisiones?</h4>
                <ul class="font-urbanist text-sm text-gray-700 space-y-2">
                    <li><strong>Comercial - Venta directa:</strong> Comisión que gana el comercial por cada ticket vendido con su código de referido.</li>
                    <li><strong>Líder - Venta directa:</strong> Comisión que gana el líder por tickets vendidos directamente con su código TV-LXXXX.</li>
                    <li><strong>Líder - Override:</strong> Comisión adicional que gana el líder por cada venta de sus comerciales.</li>
                    <li><strong>Coordinador - Override:</strong> Comisión que gana el coordinador por cada venta de los comerciales de sus líderes.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
