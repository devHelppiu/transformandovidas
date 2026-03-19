<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ticket #{{ $ticket->numero }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Ticket number display -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-indigo-50 border-4 border-indigo-200">
                        <span class="text-4xl font-mono font-bold text-indigo-700">{{ $ticket->numero }}</span>
                    </div>

                    @if($ticket->sorteo->numero_ganador === $ticket->numero && $ticket->sorteo->estado === 'ejecutado')
                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xl font-bold text-yellow-800">&#127881; ¡FELICITACIONES, ERES EL GANADOR! &#127881;</p>
                        </div>
                    @endif
                </div>

                <!-- Ticket details -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm text-gray-500">Sorteo</h3>
                        <p class="font-medium">{{ $ticket->sorteo->nombre }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-500">Fecha del sorteo</h3>
                        <p class="font-medium">{{ $ticket->sorteo->fecha_sorteo->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-500">Asignación</h3>
                        <p class="font-medium">{{ ucfirst($ticket->tipo_asignacion) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-500">Estado del ticket</h3>
                        <x-estado-badge :estado="$ticket->estado" />
                    </div>
                </div>

                <!-- Payment info -->
                @if($ticket->pago)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Información de Pago</h3>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-sm text-gray-500">Método</span>
                                <p class="font-medium">{{ ucfirst($ticket->pago->metodo) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Monto</span>
                                <p class="font-medium">${{ number_format($ticket->pago->monto, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Estado del pago</span>
                                <div class="mt-1"><x-estado-badge :estado="$ticket->pago->estado" /></div>
                            </div>
                            @if($ticket->pago->referencia_pago)
                                <div>
                                    <span class="text-sm text-gray-500">Referencia</span>
                                    <p class="font-mono">{{ $ticket->pago->referencia_pago }}</p>
                                </div>
                            @endif
                        </div>

                        @if($ticket->pago->nota_rechazo)
                            <div class="p-4 bg-red-50 rounded-lg mb-4">
                                <p class="text-sm text-red-800"><strong>Motivo de rechazo:</strong> {{ $ticket->pago->nota_rechazo }}</p>
                            </div>
                        @endif

                        <!-- Comprobante upload -->
                        @if($ticket->pago->estado === 'pendiente')
                            <div class="border-t pt-4 mt-4">
                                @if($ticket->pago->comprobante_url)
                                    <p class="text-sm text-green-600 mb-3">&#10003; Comprobante subido. Esperando verificación del administrador.</p>
                                @endif

                                <h4 class="text-sm font-medium text-gray-700 mb-3">
                                    {{ $ticket->pago->comprobante_url ? 'Reemplazar comprobante' : 'Subir comprobante de pago' }}
                                </h4>
                                <form method="POST" action="{{ route('cliente.tickets.comprobante', $ticket) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <input type="file" name="comprobante" accept=".jpg,.jpeg,.png,.pdf"
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                            <x-input-error :messages="$errors->get('comprobante')" class="mt-2" />
                                            <p class="text-xs text-gray-400 mt-1">JPG, PNG o PDF. Máximo 5MB.</p>
                                        </div>
                                        <div>
                                            <x-input-label for="referencia_pago" value="Referencia de pago (opcional)" />
                                            <x-text-input id="referencia_pago" name="referencia_pago" type="text" class="mt-1 block w-full" :value="$ticket->pago->referencia_pago" placeholder="Ej: Nequi ref. 123456" />
                                        </div>
                                        <x-primary-button>Subir Comprobante</x-primary-button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('cliente.tickets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Volver a mis tickets</a>
            </div>
        </div>
    </div>
</x-app-layout>