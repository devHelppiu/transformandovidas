<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verificar Ticket</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Verificación requerida</h3>
                    <p class="text-sm text-gray-500 mt-1">Para ver los detalles de este ticket, ingresa el correo electrónico con el que fue comprado.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Sorteo:</span> {{ $ticket->sorteo->nombre }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-medium">Número:</span> 
                        <span class="font-mono font-bold text-indigo-600">{{ $ticket->numero }}</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('ticket.verificar', $ticket) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="email" value="Correo electrónico del comprador" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autofocus placeholder="tu@correo.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <x-primary-button class="w-full justify-center py-3">
                            Verificar y Ver Ticket
                        </x-primary-button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <a href="{{ route('consulta.tickets') }}" class="text-sm text-indigo-600 hover:underline">
                        ← Buscar todos mis tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
