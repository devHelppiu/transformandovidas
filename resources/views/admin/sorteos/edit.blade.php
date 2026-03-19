<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Sorteo: {{ $sorteo->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.sorteos.update', $sorteo) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="nombre" value="Nombre del sorteo" />
                            <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $sorteo->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="descripcion" value="Descripción" />
                            <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $sorteo->descripcion) }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="fecha_sorteo" value="Fecha del sorteo" />
                                <x-text-input id="fecha_sorteo" name="fecha_sorteo" type="date" class="mt-1 block w-full" :value="old('fecha_sorteo', $sorteo->fecha_sorteo->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('fecha_sorteo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="fecha_cierre_ventas" value="Cierre de ventas" />
                                <x-text-input id="fecha_cierre_ventas" name="fecha_cierre_ventas" type="datetime-local" class="mt-1 block w-full" :value="old('fecha_cierre_ventas', $sorteo->fecha_cierre_ventas->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('fecha_cierre_ventas')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="total_tickets" value="Total tickets" />
                                <x-text-input id="total_tickets" name="total_tickets" type="number" class="mt-1 block w-full" :value="old('total_tickets', $sorteo->total_tickets)" min="1" max="10000" required />
                                <x-input-error :messages="$errors->get('total_tickets')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="precio_ticket" value="Precio ticket (COP)" />
                                <x-text-input id="precio_ticket" name="precio_ticket" type="number" step="1" min="100" class="mt-1 block w-full" :value="old('precio_ticket', $sorteo->precio_ticket)" required />
                                <x-input-error :messages="$errors->get('precio_ticket')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="valor_premio" value="Valor premio (COP)" />
                                <x-text-input id="valor_premio" name="valor_premio" type="number" step="100" min="0" class="mt-1 block w-full" :value="old('valor_premio', $sorteo->valor_premio)" />
                                <x-input-error :messages="$errors->get('valor_premio')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="compra_minima" value="Compra mínima" />
                                <x-text-input id="compra_minima" name="compra_minima" type="number" min="1" max="100" class="mt-1 block w-full" :value="old('compra_minima', $sorteo->compra_minima)" />
                                <p class="text-xs text-gray-400 mt-1">Mín. tickets por compra</p>
                                <x-input-error :messages="$errors->get('compra_minima')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Actualizar Sorteo</x-primary-button>
                            <a href="{{ route('admin.sorteos.show', $sorteo) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>