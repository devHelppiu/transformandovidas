<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 bg-tv-bg rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-tv-blue-dark">Confirmar contraseña</h1>
        <p class="mt-2 text-sm text-gray-500">
            Esta es un área segura. Por favor confirma tu contraseña para continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="'Contraseña'" class="text-gray-700 font-medium" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Confirmar
        </x-primary-button>
    </form>
</x-guest-layout>
