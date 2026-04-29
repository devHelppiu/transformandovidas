<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-tv-blue-dark">Crear cuenta</h1>
        <p class="mt-2 text-sm text-gray-500">Únete a Transformando Vidas</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="'Nombre completo'" class="text-gray-700 font-medium" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Tu nombre" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="'Correo electrónico'" class="text-gray-700 font-medium" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="tu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="'Teléfono (opcional)'" class="text-gray-700 font-medium" />
            <x-text-input id="phone" type="text" name="phone" :value="old('phone')" autocomplete="tel" placeholder="300 123 4567" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="'Contraseña'" class="text-gray-700 font-medium" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="'Confirmar contraseña'" class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contraseña" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center">
            Crear cuenta
        </x-primary-button>

        <!-- Login link -->
        <p class="text-center text-sm text-gray-500">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="font-semibold text-tv-pink hover:text-tv-pink/80 transition-colors">
                Inicia sesión
            </a>
        </p>
    </form>
</x-guest-layout>
