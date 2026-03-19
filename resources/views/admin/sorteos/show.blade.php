<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $sorteo->nombre }}</h2>
            <div class="flex gap-2">
                @if($sorteo->estado === 'borrador')
                    <form method="POST" action="{{ route('admin.sorteos.activar', $sorteo) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700" onclick="return confirm('¿Activar este sorteo?')">
                            Activar
                        </button>
                    </form>
                    <a href="{{ route('admin.sorteos.edit', $sorteo) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Editar</a>
                @elseif($sorteo->estado === 'activo')
                    <form method="POST" action="{{ route('admin.sorteos.cerrar', $sorteo) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-700" onclick="return confirm('¿Cerrar ventas de este sorteo?')">
                            Cerrar Ventas
                        </button>
                    </form>
                @elseif($sorteo->estado === 'cerrado')
                    <form method="POST" action="{{ route('admin.sorteos.ejecutar', $sorteo) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700" onclick="return confirm('¿Ejecutar este sorteo? Esta acción no se puede deshacer.')">
                            Ejecutar Sorteo
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <!-- Sorteo Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Estado</span>
                        <div class="mt-1"><x-estado-badge :estado="$sorteo->estado" /></div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Fecha del Sorteo</span>
                        <p class="text-sm font-medium">{{ $sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Precio Ticket</span>
                        <p class="text-sm font-medium">{{ '$' . number_format($sorteo->precio_ticket, 0, ',', '.') }} COP</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Premio</span>
                        <p class="text-sm font-medium">{{ '$' . number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }} COP</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Compra mínima</span>
                        <p class="text-sm font-medium">{{ $sorteo->compra_minima }} ticket{{ $sorteo->compra_minima > 1 ? 's' : '' }}</p>
                    </div>
                </div>

                @if($sorteo->numero_ganador)
                    <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-lg font-bold text-purple-800">Número Ganador: {{ $sorteo->numero_ganador }}</p>
                    </div>
                @endif

                @if($sorteo->descripcion)
                    <p class="mt-4 text-sm text-gray-600">{{ $sorteo->descripcion }}</p>
                @endif
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500">Total Tickets</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['pagados'] }}</div>
                    <div class="text-xs text-gray-500">Pagados</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['reservados'] }}</div>
                    <div class="text-xs text-gray-500">Reservados</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['anulados'] }}</div>
                    <div class="text-xs text-gray-500">Anulados</div>
                </div>
            </div>

            <!-- Combos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Combos de Tickets</h3>
                </div>
                <div class="p-6">
                    @if($sorteo->combos->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach($sorteo->combos as $combo)
                                <div class="border rounded-lg p-4 {{ $combo->activo ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200 bg-gray-50 opacity-60' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $combo->nombre }}</p>
                                            <p class="text-sm text-gray-600">{{ $combo->cantidad }} tickets</p>
                                            <p class="text-lg font-bold text-indigo-700">{{ '$' . number_format($combo->precio, 0, ',', '.') }} COP</p>
                                            @php $desc = $combo->descuento((float) $sorteo->precio_ticket); @endphp
                                            @if($desc > 0)
                                                <p class="text-xs text-green-600">{{ $desc }}% de descuento</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-1">
                                            <form method="POST" action="{{ route('admin.combos.toggle', $combo) }}">
                                                @csrf
                                                <button type="submit" class="text-xs px-2 py-1 rounded {{ $combo->activo ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                                    {{ $combo->activo ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.combos.destroy', $combo) }}" onsubmit="return confirm('¿Eliminar este combo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add combo form -->
                    <form method="POST" action="{{ route('admin.combos.store', $sorteo) }}" class="border-t pt-4">
                        @csrf
                        <p class="text-sm font-medium text-gray-700 mb-3">Agregar combo</p>
                        <div class="flex flex-wrap gap-3 items-end">
                            <div>
                                <x-input-label for="combo_nombre" value="Nombre" />
                                <x-text-input id="combo_nombre" name="nombre" type="text" class="mt-1 block w-full" placeholder="Combo 5 tickets" required />
                            </div>
                            <div>
                                <x-input-label for="combo_cantidad" value="Cantidad" />
                                <x-text-input id="combo_cantidad" name="cantidad" type="number" min="2" max="100" class="mt-1 block w-24" required />
                            </div>
                            <div>
                                <x-input-label for="combo_precio" value="Precio total (COP)" />
                                <x-text-input id="combo_precio" name="precio" type="number" min="0" step="100" class="mt-1 block w-40" required />
                            </div>
                            <x-primary-button>Agregar</x-primary-button>
                        </div>
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                    </form>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">Tickets</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comercial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sorteo->tickets->sortBy('numero') as $ticket)
                            <tr class="{{ $sorteo->numero_ganador === $ticket->numero ? 'bg-purple-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold">
                                    {{ $ticket->numero }}
                                    @if($sorteo->numero_ganador === $ticket->numero)
                                        <span class="ml-2 text-purple-600">&#9733; GANADOR</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->comprador_nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ticket->comercial?->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($ticket->tipo_asignacion) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-estado-badge :estado="$ticket->estado" /></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->pago)
                                        <x-estado-badge :estado="$ticket->pago->estado" />
                                        @if($ticket->pago->estado === 'pendiente')
                                            <a href="{{ route('admin.pagos.show', $ticket->pago) }}" class="ml-2 text-xs text-indigo-600 hover:underline">Revisar</a>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Sin pago</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay tickets.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>