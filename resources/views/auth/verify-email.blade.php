<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 bg-tv-bg rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-tv-blue-dark">Verifica tu correo</h1>
        <p class="mt-2 text-sm text-gray-500">
            ¡Gracias por registrarte! Haz clic en el enlace que enviamos a tu correo para verificar tu cuenta.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 text-center">
            ¡Listo! Hemos enviado un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center">
                Reenviar correo de verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-tv-blue transition-colors">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
