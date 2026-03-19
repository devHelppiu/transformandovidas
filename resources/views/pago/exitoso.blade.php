<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Procesado — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Nav -->
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <a href="/" class="text-xl font-bold text-indigo-600">Transformando Vidas</a>
            <a href="{{ route('consulta.tickets') }}" class="text-sm text-gray-700 hover:text-indigo-600">🎟️ Mis Tickets</a>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                {{-- Status header --}}
                @if($estadoPago === 'verificado')
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center text-white">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-1">¡Pago aprobado!</h3>
                        <p class="text-green-100">Tu compra fue procesada exitosamente.</p>
                    </div>
                @elseif($estadoPago === 'rechazado')
                    <div class="bg-gradient-to-r from-red-500 to-rose-600 p-8 text-center text-white">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-1">Pago rechazado</h3>
                        <p class="text-red-100">Tu pago no pudo ser procesado. Puedes intentarlo de nuevo.</p>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-yellow-500 to-amber-500 p-8 text-center text-white">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-1">Procesando pago…</h3>
                        <p class="text-yellow-100">Estamos verificando tu transacción. Esta página se actualizará automáticamente.</p>
                    </div>
                @endif

                <div class="p-6 sm:p-8 space-y-6">

                    {{-- Order summary --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Resumen de compra</h4>
                        <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Sorteo</span>
                                <span class="font-semibold text-gray-900">{{ $sorteo?->nombre }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Fecha del sorteo</span>
                                <span class="font-semibold text-gray-900">{{ $sorteo?->fecha_sorteo->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tickets comprados</span>
                                <span class="font-semibold text-gray-900">{{ $tickets->count() }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between">
                                <span class="font-bold text-gray-900">Total pagado</span>
                                <span class="font-bold text-lg text-indigo-600">{{ '$' . number_format($totalPagado, 0, ',', '.') }} COP</span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment status --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Estado del pago</h4>
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                            @if($estadoPago === 'verificado')
                                <span class="flex-shrink-0 w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="font-semibold text-green-700">Aprobado</span>
                            @elseif($estadoPago === 'rechazado')
                                <span class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="font-semibold text-red-700">Rechazado</span>
                            @elseif($estadoPago === 'procesando')
                                <span class="flex-shrink-0 w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span>
                                <span class="font-semibold text-blue-700">En proceso (PSE/banco)</span>
                            @elseif($estadoPago === 'reversado')
                                <span class="flex-shrink-0 w-3 h-3 rounded-full bg-orange-500"></span>
                                <span class="font-semibold text-orange-700">Reversado</span>
                            @else
                                <span class="flex-shrink-0 w-3 h-3 rounded-full bg-yellow-500 animate-pulse"></span>
                                <span class="font-semibold text-yellow-700">Pendiente de verificación</span>
                            @endif
                        </div>
                    </div>

                    {{-- Tickets list --}}
                    @if($tickets->isNotEmpty())
                        <div>
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Tus tickets</h4>
                            <div class="space-y-2">
                                @foreach($tickets as $ticket)
                                    <div class="flex justify-between items-center bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl font-mono font-bold text-indigo-700">#{{ str_pad($ticket->numero, 4, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-sm text-gray-500">{{ $sorteo?->nombre }}</span>
                                        </div>
                                        @if($ticket->estado === 'pagado')
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-700">✓ Confirmado</span>
                                        @elseif($ticket->estado === 'reservado')
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">⏳ Procesando</span>
                                        @else
                                            <span class="text-xs font-medium px-3 py-1 rounded-full bg-red-100 text-red-700">✗ {{ ucfirst($ticket->estado) }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Info note --}}
                    @if(!in_array($estadoPago, ['verificado', 'rechazado', 'reversado']))
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
                            <strong>💡 Nota:</strong> Tu pago está siendo verificado por Helppiu Pay. Esta página se actualiza automáticamente cada 5 segundos. También puedes consultar tus tickets con tu correo electrónico.
                        </div>
                    @endif

                    @if($estadoPago === 'rechazado')
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                            <strong>⚠️</strong> Tu pago fue rechazado. Puedes volver al sorteo e intentar de nuevo.
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <a href="{{ route('consulta.tickets') }}" class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm hover:bg-indigo-700 transition">
                            🎟️ Consultar mis tickets
                        </a>
                        <a href="/" class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>

            {{-- Reference --}}
            <p class="text-center text-xs text-gray-400 mt-4">Referencia: TV-{{ Str::limit($grupo, 8, '') }}</p>
        </div>
    </div>

    {{-- Auto-refresh if payment is still pending or processing --}}
    @if(in_array($estadoPago, ['pendiente', 'procesando']))
        <script>
            setTimeout(function() { window.location.reload(); }, 5000);
        </script>
    @endif
</body>
</html>
