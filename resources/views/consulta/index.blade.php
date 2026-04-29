<x-public-layout>
    <x-slot name="title">Mis Tickets — {{ config('app.name') }}</x-slot>

    <div class="bg-tv-bg min-h-screen py-16">
        <div class="max-w-lg mx-auto px-4 sm:px-6">

            <x-flash-messages />

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-tv-bg rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <h1 class="font-urbanist font-bold text-tv-blue-dark text-2xl">Mis Tickets</h1>
                    <p class="font-urbanist text-sm text-gray-500 mt-2">
                        Ingresa el correo electrónico que usaste al momento de la compra.
                    </p>
                </div>

                <form method="POST" action="{{ route('consulta.tickets.buscar') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block font-urbanist text-sm font-medium text-gray-700 mb-1.5">
                                Correo electrónico
                            </label>
                            <input id="email" name="email" type="email"
                                   class="w-full rounded-xl border-gray-200 focus:border-tv-blue focus:ring-tv-blue py-3 px-4 font-urbanist text-sm"
                                   value="{{ old('email') }}" required autofocus placeholder="tu@correo.com"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm transition-all shadow-lg shadow-tv-blue/20 active:scale-[0.98]">
                            Buscar mis tickets
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-public-layout>
