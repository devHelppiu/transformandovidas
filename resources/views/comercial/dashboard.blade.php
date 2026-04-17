<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Comercial</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Referral Info -->
            <div class="bg-indigo-50 border border-indigo-200 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-indigo-900 mb-3">Tu enlace de referido</h3>
                
                <!-- Enlace universal -->
                <div class="mb-4">
                    <p class="text-sm text-indigo-700 mb-2">Enlace universal (redirige automáticamente al sorteo activo):</p>
                    <div class="flex items-center gap-4" x-data="{ copied: false }">
                        <div class="flex-1 bg-white rounded-md border border-indigo-300 px-4 py-2 font-mono text-sm text-indigo-800 truncate">
                            {{ $enlace }}
                        </div>
                        <button @click="navigator.clipboard.writeText('{{ $enlace }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 whitespace-nowrap">
                            <span x-show="!copied">Copiar</span>
                            <span x-show="copied" x-cloak>¡Copiado!</span>
                        </button>
                    </div>
                </div>

                <!-- Enlaces específicos por sorteo -->
                @if($sorteosActivos->count() > 0)
                    <div class="border-t border-indigo-200 pt-4 mt-4">
                        <p class="text-sm text-indigo-700 mb-3">O usa enlaces específicos por sorteo:</p>
                        <div class="space-y-2">
                            @foreach($sorteosActivos as $sorteo)
                                @php $enlaceSorteo = route('sorteo.publico', ['sorteo' => $sorteo, 'ref' => $comercial->codigo_ref]); @endphp
                                <div class="flex items-center gap-3" x-data="{ copied: false }">
                                    <span class="text-sm font-medium text-gray-700 w-48 truncate">{{ $sorteo->nombre }}</span>
                                    <div class="flex-1 bg-white rounded-md border border-gray-300 px-3 py-1.5 font-mono text-xs text-gray-600 truncate">
                                        {{ $enlaceSorteo }}
                                    </div>
                                    <button @click="navigator.clipboard.writeText('{{ $enlaceSorteo }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="bg-gray-600 text-white px-3 py-1.5 rounded text-xs hover:bg-gray-700 whitespace-nowrap">
                                        <span x-show="!copied">Copiar</span>
                                        <span x-show="copied" x-cloak>✓</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-yellow-700 mt-3">⚠️ No hay sorteos activos en este momento.</p>
                @endif

                <p class="mt-4 text-sm text-indigo-700">
                    Tu código: <span class="font-mono font-bold bg-white px-2 py-1 rounded">{{ $comercial->codigo_ref }}</span>
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Tickets Referidos</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_referidos'] }}</div>
                    <div class="text-xs text-gray-400">tickets pagados</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Clientes Referidos</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_clientes'] }}</div>
                    <div class="text-xs text-gray-400">clientes únicos</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Monto Recaudado</div>
                    <div class="text-3xl font-bold text-green-600">${{ number_format($stats['monto_recaudado'], 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Comisión info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-2">Tu comisión</h3>
                @if($comercial->comision_tipo)
                    <p class="text-gray-700">
                        Tipo: <span class="font-medium">{{ ucfirst($comercial->comision_tipo) }}</span> —
                        Valor: <span class="font-medium">
                            {{ $comercial->comision_tipo === 'porcentaje' ? $comercial->comision_valor . '%' : '$' . number_format($comercial->comision_valor, 0, ',', '.') }}
                        </span>
                    </p>
                    <a href="{{ route('comercial.comisiones.index') }}" class="text-sm text-indigo-600 hover:underline mt-2 inline-block">Ver detalle de comisiones &rarr;</a>
                @else
                    <p class="text-gray-500">Tu comisión aún no ha sido configurada. Contacta al administrador.</p>
                @endif
            </div>

            <!-- Tickets por sorteo -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Últimos tickets referidos</h3>
                @forelse($ticketsPorSorteo as $sorteoId => $tickets)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">{{ $tickets->first()->sorteo->nombre }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tickets as $ticket)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 font-mono">
                                    {{ $ticket->numero }}
                                    <span class="ml-1 text-xs text-gray-500">({{ $ticket->comprador_nombre }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Aún no tienes tickets referidos.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>