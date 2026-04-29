<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Sorteo: {{ $sorteo->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.sorteos.update', $sorteo) }}" enctype="multipart/form-data">
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

                        <div>
                            <x-input-label value="Imagen del sorteo (opcional)" />
                            @if($sorteo->imagen)
                                <div class="mt-1 flex items-start gap-4 p-3 bg-gray-50 rounded-md">
                                    <img src="{{ asset('storage/' . $sorteo->imagen) }}" alt="{{ $sorteo->nombre }}" class="h-24 w-auto rounded-md border border-gray-200">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-700 font-medium mb-1">Imagen actual</p>
                                        <p class="text-xs text-gray-500 mb-2">{{ basename($sorteo->imagen) }}</p>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" name="eliminar_imagen" value="0">
                                            <input type="checkbox" name="eliminar_imagen" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                            <span class="text-xs text-red-600">Eliminar imagen actual</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <input id="imagen" name="imagen" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-tv-blue/10 file:text-tv-blue hover:file:bg-tv-blue/20">
                            <p class="text-xs text-gray-400 mt-1">
                                @if($sorteo->imagen)
                                    Subir un archivo nuevo reemplazará la imagen actual.
                                @else
                                    JPG, PNG o WebP. Máximo 2 MB.
                                @endif
                            </p>
                            <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
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

                        <div>
                            <x-input-label for="premio_extra" value="Premio extra (opcional)" />
                            <x-text-input id="premio_extra" name="premio_extra" type="text" maxlength="80" class="mt-1 block w-full" :value="old('premio_extra', $sorteo->premio_extra)" placeholder='Ej: "+ Camioneta"' />
                            <p class="text-xs text-gray-400 mt-1">Texto corto que aparecerá junto al monto del premio en la home (p. ej. "+ Camioneta").</p>
                            <x-input-error :messages="$errors->get('premio_extra')" class="mt-2" />
                        </div>

                        <!-- Pago Simulado (Demo) -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="pago_simulado" value="0">
                                <input type="checkbox" name="pago_simulado" value="1" class="rounded border-yellow-400 text-yellow-600 focus:ring-yellow-500" {{ old('pago_simulado', $sorteo->pago_simulado) ? 'checked' : '' }}>
                                <div>
                                    <span class="font-medium text-yellow-800">🎬 Modo Demo (Pago Simulado)</span>
                                    <p class="text-xs text-yellow-600">Al comprar, el pago se aprueba automáticamente sin ir a la pasarela real. Ideal para demos y videos.</p>
                                </div>
                            </label>
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