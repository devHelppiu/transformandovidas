<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Comercial: {{ $comercial->user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 p-4 bg-indigo-50 rounded-lg">
                    <p class="text-sm text-indigo-800">Código de referido: <span class="font-mono font-bold">{{ $comercial->codigo_ref }}</span></p>
                </div>

                <form method="POST" action="{{ route('admin.comerciales.update', $comercial) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" value="Nombre completo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $comercial->user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" value="Correo electrónico" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $comercial->user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" value="Teléfono" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $comercial->user->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="comision_tipo" value="Tipo de comisión" />
                                <select id="comision_tipo" name="comision_tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Sin configurar</option>
                                    <option value="porcentaje" {{ old('comision_tipo', $comercial->comision_tipo) === 'porcentaje' ? 'selected' : '' }}>Porcentaje</option>
                                    <option value="fijo" {{ old('comision_tipo', $comercial->comision_tipo) === 'fijo' ? 'selected' : '' }}>Fijo por ticket</option>
                                    <option value="meta" {{ old('comision_tipo', $comercial->comision_tipo) === 'meta' ? 'selected' : '' }}>Meta (pendiente)</option>
                                </select>
                                <x-input-error :messages="$errors->get('comision_tipo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="comision_valor" value="Valor comisión" />
                                <x-text-input id="comision_valor" name="comision_valor" type="number" step="0.01" class="mt-1 block w-full" :value="old('comision_valor', $comercial->comision_valor)" />
                                <x-input-error :messages="$errors->get('comision_valor')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Actualizar Comercial</x-primary-button>
                            <a href="{{ route('admin.comerciales.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>