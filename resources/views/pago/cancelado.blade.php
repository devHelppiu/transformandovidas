<x-public-layout>
<x-slot name="title">Pago Cancelado — {{ config('app.name') }}</x-slot>

    <div class="bg-tv-bg min-h-screen py-14">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-red-500 p-8 text-center text-white">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="font-urbanist font-bold text-2xl mb-1">Pago cancelado</h3>
                    <p class="font-urbanist text-white/80">El proceso de pago fue cancelado. Tus números quedan reservados hasta que el sistema los libere.</p>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="bg-tv-bg rounded-xl p-4 font-urbanist text-sm text-gray-600 mb-6">
                        No se realizó ningún cargo a tu cuenta. Si fue un error, puedes volver al sorteo e intentar nuevamente.
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($sorteo)
                            <a href="{{ route('sorteo.publico', $sorteo) }}"
                               class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-tv-blue text-white rounded-xl font-urbanist font-semibold text-sm hover:bg-tv-blue/90 transition">
                                🔁 Intentar de nuevo
                            </a>
                        @endif
                        <a href="/"
                           class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-urbanist font-semibold text-sm hover:bg-gray-200 transition">
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-public-layout>
