<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Revisar Pago</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Payment details -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Cliente</h3>
                        <p class="text-lg">{{ $pago->ticket->comprador_nombre }}</p>
                        <p class="text-sm text-gray-500">{{ $pago->ticket->comprador_email }}</p>
                        <p class="text-sm text-gray-500">{{ $pago->ticket->comprador_telefono }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Ticket</h3>
                        <p class="text-lg font-mono font-bold">{{ $pago->ticket->numero }}</p>
                        <p class="text-sm text-gray-500">{{ $pago->ticket->sorteo->nombre }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Método de pago</h3>
                        <p class="text-lg">{{ ucfirst($pago->metodo) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Monto</h3>
                        <p class="text-lg font-bold">${{ number_format($pago->monto, 0, ',', '.') }}</p>
                    </div>
                    @if($pago->referencia_pago)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Referencia</h3>
                            <p class="text-lg font-mono">{{ $pago->referencia_pago }}</p>
                        </div>
                    @endif
                    @if($pago->ticket->comercial)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Comercial referido</h3>
                            <p class="text-lg">{{ $pago->ticket->comercial->user->name }}</p>
                            <p class="text-sm text-gray-500 font-mono">{{ $pago->ticket->comercial->codigo_ref }}</p>
                        </div>
                    @endif
                </div>

                <!-- Comprobante -->
                @if($pago->comprobante_url)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Comprobante</h3>
                        <div class="border rounded-lg p-2">
                            @if(str_ends_with($pago->comprobante_url, '.pdf'))
                                <a href="{{ route('admin.ticket.comprobante.ver', $pago->ticket) }}" target="_blank" class="text-indigo-600 hover:underline">Ver PDF del comprobante</a>
                            @else
                                <img src="{{ route('admin.ticket.comprobante.ver', $pago->ticket) }}" alt="Comprobante" class="max-w-full max-h-96 mx-auto rounded">
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Estado actual -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Estado actual</h3>
                    <x-estado-badge :estado="$pago->estado" />

                    @if($pago->nota_rechazo)
                        <p class="mt-2 text-sm text-red-600">Motivo: {{ $pago->nota_rechazo }}</p>
                    @endif
                </div>

                <!-- Actions -->
                @if($pago->estado === 'pendiente')
                    <div class="border-t pt-6 space-y-4">
                        <form method="POST" action="{{ route('admin.pagos.verificar', $pago) }}">
                            @csrf
                            <input type="hidden" name="accion" value="verificar">
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-3 rounded-md text-sm font-medium hover:bg-green-700" onclick="return confirm('¿Verificar este pago?')">
                                ✓ Verificar Pago
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.pagos.verificar', $pago) }}" x-data="{ show: false }">
                            @csrf
                            <input type="hidden" name="accion" value="rechazar">
                            <button type="button" @click="show = !show" class="w-full bg-red-600 text-white px-4 py-3 rounded-md text-sm font-medium hover:bg-red-700">
                                ✕ Rechazar Pago
                            </button>
                            <div x-show="show" x-cloak class="mt-3">
                                <textarea name="nota_rechazo" rows="3" placeholder="Motivo del rechazo..." class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm" required></textarea>
                                <x-input-error :messages="$errors->get('nota_rechazo')" class="mt-2" />
                                <button type="submit" class="mt-2 bg-red-800 text-white px-4 py-2 rounded-md text-sm hover:bg-red-900" onclick="return confirm('¿Confirmar rechazo?')">
                                    Confirmar Rechazo
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('admin.pagos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Volver a pagos</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>