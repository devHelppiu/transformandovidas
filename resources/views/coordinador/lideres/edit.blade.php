<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Líder</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="mb-6 p-4 bg-tv-bg rounded-xl">
                    <p class="font-urbanist text-sm text-gray-600">
                        Código de referido: <span class="font-mono font-bold text-tv-blue">{{ $lider->codigo_ref }}</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('coordinador.lideres.update', $lider) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nombre completo" />
                        <x-text-input id="name" name="name" type="text" :value="old('name', $lider->user->name)" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" :value="old('email', $lider->user->email)" required class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Teléfono (opcional)" />
                        <x-text-input id="phone" name="phone" type="text" :value="old('phone', $lider->user->phone)" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Nueva contraseña (dejar vacío para mantener)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <x-primary-button>Guardar cambios</x-primary-button>
                        <a href="{{ route('coordinador.lideres.index') }}" class="font-urbanist text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
