<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Comercial</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mi Líder --}}
            @if($comercial->lider)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="font-urbanist font-bold text-gray-900 mb-3">Mi Líder</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-tv-bg flex items-center justify-center">
                        <span class="font-urbanist font-bold text-tv-blue">
                            {{ substr($comercial->lider->user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-urbanist font-semibold text-gray-900">{{ $comercial->lider->user->name }}</p>
                        <p class="font-urbanist text-sm text-gray-500">{{ $comercial->lider->user->email }}</p>
                        @if($comercial->lider->user->phone)
                            <p class="font-urbanist text-xs text-gray-400">{{ $comercial->lider->user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Enlace de Referido --}}
            <div class="bg-tv-bg border border-tv-blue/20 rounded-2xl p-6 mb-6">
                <h3 class="font-urbanist font-bold text-tv-blue-dark text-lg mb-3">Tu enlace de referido</h3>
                
                {{-- Enlace universal --}}
                <div class="mb-4">
                    <p class="font-urbanist text-sm text-tv-blue mb-2">Enlace universal (redirige automáticamente al sorteo activo):</p>
                    <div class="flex items-center gap-4" x-data="{ copied: false }">
                        <div class="flex-1 bg-white rounded-xl border border-tv-blue/20 px-4 py-2.5 font-mono text-sm text-tv-blue-dark truncate">
                            {{ $enlace }}
                        </div>
                        <button @click="navigator.clipboard.writeText('{{ $enlace }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-tv-blue hover:bg-tv-blue/90 text-white px-5 py-2.5 rounded-xl font-urbanist font-bold text-sm whitespace-nowrap transition-colors">
                            <span x-show="!copied">Copiar</span>
                            <span x-show="copied" x-cloak>¡Copiado!</span>
                        </button>
                    </div>
                </div>

                {{-- Enlaces específicos por sorteo --}}
                @if($sorteosActivos->count() > 0)
                    <div class="border-t border-tv-blue/20 pt-4 mt-4">
                        <p class="font-urbanist text-sm text-tv-blue mb-3">O usa enlaces específicos por sorteo:</p>
                        <div class="space-y-2">
                            @foreach($sorteosActivos as $sorteo)
                                @php $enlaceSorteo = route('sorteo.publico', ['sorteo' => $sorteo, 'ref' => $comercial->codigo_ref]); @endphp
                                <div class="flex items-center gap-3" x-data="{ copied: false }">
                                    <span class="font-urbanist text-sm font-medium text-gray-700 w-48 truncate">{{ $sorteo->nombre }}</span>
                                    <div class="flex-1 bg-white rounded-lg border border-gray-200 px-3 py-1.5 font-mono text-xs text-gray-600 truncate">
                                        {{ $enlaceSorteo }}
                                    </div>
                                    <button @click="navigator.clipboard.writeText('{{ $enlaceSorteo }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-lg font-urbanist text-xs whitespace-nowrap transition-colors">
                                        <span x-show="!copied">Copiar</span>
                                        <span x-show="copied" x-cloak>✓</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="font-urbanist text-sm text-amber-700 mt-3">⚠️ No hay sorteos activos en este momento.</p>
                @endif

                <p class="font-urbanist mt-4 text-sm text-tv-blue">
                    Tu código: <span class="font-mono font-bold bg-white px-2.5 py-1 rounded-lg">{{ $comercial->codigo_ref }}</span>
                </p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Tickets Referidos</p>
                    <p class="font-urbanist font-bold text-3xl text-tv-blue mt-1">{{ $stats['total_referidos'] }}</p>
                    <p class="font-urbanist text-xs text-gray-400">tickets pagados</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Clientes Referidos</p>
                    <p class="font-urbanist font-bold text-3xl text-tv-blue-dark mt-1">{{ $stats['total_clientes'] }}</p>
                    <p class="font-urbanist text-xs text-gray-400">clientes únicos</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <p class="font-urbanist text-sm text-gray-500">Monto Recaudado</p>
                    <p class="font-urbanist font-bold text-3xl text-green-600 mt-1">${{ number_format($stats['monto_recaudado'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Comisión info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <h3 class="font-urbanist font-bold text-gray-900 mb-3">Tu comisión</h3>
                @php
                    $config = \App\Models\ComisionConfig::obtenerConfig('comercial', 'directo');
                @endphp
                @if($config)
                    <p class="font-urbanist text-gray-700">
                        Tu comisión: <span class="font-bold text-tv-blue">{{ number_format($config->valor, 2) }}{{ $config->tipo === 'porcentaje' ? '%' : ' COP' }}</span> por ticket
                    </p>
                    <a href="{{ route('comercial.comisiones.index') }}" class="font-urbanist text-sm text-tv-blue hover:underline mt-2 inline-block">Ver detalle de comisiones →</a>
                @elseif($comercial->comision_tipo)
                    <p class="font-urbanist text-gray-700">
                        Tipo: <span class="font-medium">{{ ucfirst($comercial->comision_tipo) }}</span> —
                        Valor: <span class="font-medium">
                            {{ $comercial->comision_tipo === 'porcentaje' ? $comercial->comision_valor . '%' : '$' . number_format($comercial->comision_valor, 0, ',', '.') }}
                        </span>
                    </p>
                    <a href="{{ route('comercial.comisiones.index') }}" class="font-urbanist text-sm text-tv-blue hover:underline mt-2 inline-block">Ver detalle de comisiones →</a>
                @else
                    <p class="font-urbanist text-gray-500">Tu comisión aún no ha sido configurada. Contacta al administrador.</p>
                @endif
            </div>

            {{-- Tickets por sorteo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-urbanist font-bold text-gray-900 mb-4">Últimos tickets referidos</h3>
                @forelse($ticketsPorSorteo as $sorteoId => $tickets)
                    <div class="mb-4">
                        <h4 class="font-urbanist text-sm font-medium text-gray-500 mb-2">{{ $tickets->first()->sorteo->nombre }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tickets as $ticket)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm bg-tv-bg font-mono text-tv-blue-dark">
                                    {{ $ticket->numero }}
                                    <span class="ml-1 text-xs text-gray-500">({{ $ticket->comprador_nombre }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="font-urbanist text-gray-500">Aún no tienes tickets referidos.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>