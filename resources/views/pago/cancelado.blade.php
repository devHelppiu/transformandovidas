<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Cancelado — {{ config('app.name') }}</title>
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
                <div class="bg-gradient-to-r from-red-500 to-rose-600 p-8 text-center text-white">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-1">Pago cancelado</h3>
                    <p class="text-red-100">El proceso de pago fue cancelado. Tus números quedan reservados hasta que el sistema los libere.</p>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 mb-6">
                        No se realizó ningún cargo a tu cuenta. Si fue un error, puedes volver al sorteo e intentar nuevamente.
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($sorteo)
                            <a href="{{ route('sorteo.publico', $sorteo) }}" class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white rounded-xl font-semibold text-sm hover:bg-indigo-700 transition">
                                🔁 Intentar de nuevo
                            </a>
                        @endif
                        <a href="/" class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
