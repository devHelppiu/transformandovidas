<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-tv-blue-dark">Recuperar contraseña</h1>
        <p class="mt-2 text-sm text-gray-500">Te enviaremos un enlace para restablecer tu contraseña</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="'Correo electrónico'" class="text-gray-700 font-medium" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="tu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Enviar enlace de recuperación
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="font-semibold text-tv-blue hover:text-tv-blue-dark transition-colors">
                ← Volver al inicio de sesión
            </a>
        </p>
    </form>
</x-guest-layout>
