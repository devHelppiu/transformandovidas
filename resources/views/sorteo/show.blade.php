<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $sorteo->nombre }}</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            @if($sorteo->estado === 'ejecutado')
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-8 text-center">
                    <p class="text-lg text-purple-800">Este sorteo ya fue ejecutado.</p>
                    <p class="text-4xl font-mono font-bold text-purple-900 mt-3">Número ganador: {{ $sorteo->numero_ganador }}</p>
                </div>
            @elseif($sorteo->estado !== 'activo')
                <div class="bg-gray-50 border rounded-2xl p-8 text-center">
                    <p class="text-lg text-gray-600">
                        @if($sorteo->estado === 'cerrado')
                            Las ventas están cerradas. El sorteo se ejecutará pronto.
                        @else
                            Este sorteo aún no está activo.
                        @endif
                    </p>
                </div>
            @else
                <div x-data="cart()">
                    <form method="POST" action="{{ route('tickets.comprar', $sorteo) }}">
                        @csrf

                        {{-- Stepper visual --}}
                        <div class="flex items-center justify-center gap-2 mb-8">
                            <template x-for="(label, i) in ['Tickets', 'Datos y pago']" :key="i">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                                              :class="step > i + 1 ? 'bg-green-500 text-white' : step === i + 1 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-gray-200 text-gray-500'">
                                            <template x-if="step > i + 1"><span>&#10003;</span></template>
                                            <template x-if="step <= i + 1"><span x-text="i + 1"></span></template>
                                        </span>
                                        <span class="text-sm font-medium hidden sm:inline" :class="step === i + 1 ? 'text-indigo-700' : 'text-gray-400'" x-text="label"></span>
                                    </div>
                                    <div x-show="i < 1" class="w-8 sm:w-16 h-0.5 rounded" :class="step > i + 1 ? 'bg-green-400' : 'bg-gray-200'"></div>
                                </div>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                            {{-- LEFT: Steps content --}}
                            <div class="lg:col-span-2 space-y-6">

                                {{-- STEP 1: Select tickets --}}
                                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                        <div class="flex items-center gap-3 mb-5">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">Selecciona tus tickets</h3>
                                                <p class="text-sm text-gray-500">Números asignados al azar automáticamente</p>
                                            </div>
                                        </div>

                                        {{-- Quantity selector --}}
                                        <div class="mb-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">¿Cuántos tickets quieres?</label>
                                            @if($sorteo->compra_minima > 1)
                                                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-3 text-sm text-amber-700">
                                                    ⚡ Compra mínima: <strong>{{ $sorteo->compra_minima }} tickets</strong>
                                                </div>
                                            @endif
                                            <div class="flex items-center justify-center gap-4">
                                                <button type="button" @click="decrementQty()" class="w-12 h-12 rounded-xl border-2 text-xl font-bold transition-all duration-200 flex items-center justify-center"
                                                        :class="parseInt(cantidad) <= minQty ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 active:scale-95'">
                                                    −
                                                </button>
                                                <div class="text-center">
                                                    <span class="text-4xl font-black text-indigo-700" x-text="cantidad"></span>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="parseInt(cantidad) === 1 ? 'ticket' : 'tickets'"></p>
                                                </div>
                                                <button type="button" @click="incrementQty()" class="w-12 h-12 rounded-xl border-2 border-indigo-200 text-xl font-bold text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition-all duration-200 flex items-center justify-center active:scale-95">
                                                    +
                                                </button>
                                            </div>
                                            <p class="text-center text-sm text-gray-500 mt-2">
                                                <span x-text="formatPrice(parseInt(cantidad) * precioBase)"></span> — <span class="text-gray-400" x-text="formatPrice(precioBase) + ' c/u'"></span>
                                            </p>
                                        </div>

                                        {{-- Combos as discount presets --}}
                                        @if($combos->where('activo', true)->isNotEmpty())
                                            <div class="border-t border-gray-100 pt-4 mt-2">
                                                <p class="text-sm font-medium text-gray-700 mb-3">🏷️ Combos con descuento</p>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                    @foreach($combos as $combo)
                                                        @if($combo->activo && $combo->cantidad >= $sorteo->compra_minima)
                                                        <div class="cursor-pointer group" @click="selectCombo('{{ $combo->cantidad }}', '{{ $combo->id }}', {{ $combo->precio }})">
                                                            <div class="relative rounded-2xl p-4 text-center transition-all duration-200 border-2"
                                                                 :class="comboId === '{{ $combo->id }}' ? 'border-indigo-600 bg-indigo-50 shadow-md shadow-indigo-100 scale-[1.02]' : 'border-gray-100 bg-white hover:border-indigo-200 hover:shadow-sm'">
                                                                @php $desc = $combo->descuento((float) $sorteo->precio_ticket); @endphp
                                                                @if($desc > 0)
                                                                    <span class="absolute -top-2 -right-2 text-[10px] font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white px-2 py-0.5 rounded-full shadow-sm">-{{ $desc }}%</span>
                                                                @endif
                                                                <div class="text-3xl font-black text-gray-800" :class="comboId === '{{ $combo->id }}' && 'text-indigo-700'">{{ $combo->cantidad }}</div>
                                                                <div class="text-xs text-gray-500 mt-0.5">tickets</div>
                                                                <div class="text-base font-bold mt-2" :class="comboId === '{{ $combo->id }}' ? 'text-indigo-700' : 'text-gray-700'">{{ '$' . number_format($combo->precio, 0, ',', '.') }}</div>
                                                                <div class="text-[10px] text-gray-400 mt-1">{{ '$' . number_format($combo->precio / $combo->cantidad, 0, ',', '.') }} c/u</div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <x-input-error :messages="$errors->get('cantidad')" class="mt-3" />
                                        <x-input-error :messages="$errors->get('combo_id')" class="mt-2" />
                                        <input type="hidden" name="cantidad" x-model="cantidad">
                                        <input type="hidden" name="combo_id" x-model="comboId">

                                        <button type="button" @click="if(selected) step = 2" class="mt-5 w-full py-3 rounded-xl font-semibold text-sm transition-all duration-200"
                                                :class="selected ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">
                                            Continuar
                                        </button>
                                    </div>
                                </div>

                                {{-- STEP 2: Buyer data --}}
                                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                        <div class="flex items-center gap-3 mb-5">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">Tus datos</h3>
                                                <p class="text-sm text-gray-500">No necesitas crear cuenta</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                                <input type="text" name="nombre" x-model="nombre" value="{{ old('nombre') }}" required placeholder="Tu nombre completo"
                                                       class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm" />
                                                <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                                    <input type="email" name="email" x-model="email" value="{{ old('email') }}" required placeholder="tu@correo.com"
                                                           class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm" />
                                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-gray-400">(opcional)</span></label>
                                                    <input type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="300 123 4567"
                                                           class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm" />
                                                    <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
                                                </div>
                                            </div>
                                            @if(!session('referral_comercial_id'))
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de referido <span class="text-gray-400">(opcional)</span></label>
                                                    <input type="text" name="codigo_referido" value="{{ old('codigo_referido') }}" placeholder="TV-XXXXXX"
                                                           class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-sm" />
                                                    <x-input-error :messages="$errors->get('codigo_referido')" class="mt-1" />
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex gap-3 mt-5">
                                            <button type="button" @click="step = 1" class="px-5 py-3 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                                                ← Atrás
                                            </button>
                                            <button type="submit" class="flex-1 py-3.5 rounded-xl font-bold text-sm transition-all duration-200"
                                                    :class="nombre.trim() && email.trim() ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-200 active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                                    :disabled="!(nombre.trim() && email.trim())">
                                                🔒 <span x-text="buttonText"></span>
                                            </button>
                                        </div>

                                        @if(session('referral_codigo'))
                                            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 flex items-center gap-2 text-sm text-green-700">
                                                <span>✓</span> Referido: <span class="font-mono font-bold">{{ session('referral_codigo') }}</span>
                                            </div>
                                        @endif

                                        <x-input-error :messages="$errors->get('sorteo')" class="mt-2" />

                                        <p class="text-xs text-center text-gray-400 mt-4">
                                            Serás redirigido a <strong>Helppiu Pay</strong> para completar el pago de forma segura.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: Order summary (sticky) --}}
                            <div class="lg:col-span-1">
                                <div class="sticky top-24 space-y-4">
                                    {{-- Prize card --}}
                                    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 rounded-2xl p-5 text-white shadow-xl shadow-indigo-200">
                                        <p class="text-indigo-200 text-xs font-medium uppercase tracking-wider">Premio</p>
                                        <p class="text-3xl font-black mt-1">{{ '$' . number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }}</p>
                                        <div class="mt-3 flex items-center gap-2 text-sm text-indigo-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ $sorteo->fecha_sorteo->format('d M Y') }}
                                        </div>
                                        @php $pct = $sorteo->total_tickets > 0 ? round(($ticketsVendidos / $sorteo->total_tickets) * 100, 1) : 0; @endphp
                                        <div class="mt-4">
                                            <div class="flex justify-between text-xs text-indigo-200 mb-1">
                                                <span>Vendidos</span>
                                                <span>{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full bg-white/20 rounded-full h-2">
                                                <div class="h-2 rounded-full bg-white transition-all" style="width: {{ max($pct, 1) }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Order summary --}}
                                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                        <h4 class="font-bold text-gray-900 mb-4">Resumen de compra</h4>
                                        <div x-show="!selected" class="text-center py-6">
                                            <div class="text-3xl mb-2">🎟️</div>
                                            <p class="text-sm text-gray-400">Selecciona la cantidad de tickets</p>
                                        </div>
                                        <div x-show="selected" class="space-y-3">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Tickets</span>
                                                <span class="font-medium text-gray-900" x-text="(parseInt(cantidad) || 1) + 'x'"></span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Precio unitario</span>
                                                <span class="font-medium text-gray-900" x-text="formatPrice(unitPrice)"></span>
                                            </div>
                                            <template x-if="savings > 0">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-green-600">Ahorro</span>
                                                    <span class="font-medium text-green-600" x-text="'-' + formatPrice(savings)"></span>
                                                </div>
                                            </template>
                                            <div class="border-t border-dashed pt-3 flex justify-between">
                                                <span class="font-bold text-gray-900">Total</span>
                                                <span class="font-black text-lg text-indigo-700" x-text="formatPrice(totalPrice)"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Trust badges --}}
                                    <div class="flex items-center justify-center gap-4 text-xs text-gray-400 px-2">
                                        <span class="flex items-center gap-1">🔒 Helppiu Pay</span>
                                        <span class="flex items-center gap-1">💳 Tarjetas · PSE · Nequi</span>
                                        <span class="flex items-center gap-1">📧 Confirmación</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<script>
    function cart() {
        const precioUnitario = {{ $sorteo->precio_ticket }};
        const minCompra = {{ $sorteo->compra_minima }};
        const combosMap = {
            @foreach($combos->where('activo', true) as $c)
                '{{ $c->id }}': { cantidad: {{ $c->cantidad }}, precio: {{ $c->precio }} },
            @endforeach
        };
        return {
            step: 1,
            cantidad: Math.max(minCompra, 1),
            comboId: '',
            selected: true,
            precioBase: precioUnitario,
            minQty: Math.max(minCompra, 1),
            nombre: '{{ old("nombre", "") }}',
            email: '{{ old("email", "") }}',
            incrementQty() {
                this.cantidad = parseInt(this.cantidad) + 1;
                this.comboId = '';
            },
            decrementQty() {
                const current = parseInt(this.cantidad);
                if (current > this.minQty) {
                    this.cantidad = current - 1;
                    this.comboId = '';
                }
            },
            selectCombo(qty, comboId, price) {
                this.cantidad = parseInt(qty);
                this.comboId = comboId;
            },
            get activeCombo() {
                return this.comboId ? combosMap[this.comboId] : null;
            },
            get totalPrice() {
                if (this.activeCombo) return this.activeCombo.precio;
                return precioUnitario * parseInt(this.cantidad);
            },
            get unitPrice() {
                const qty = parseInt(this.cantidad) || 1;
                return this.totalPrice / qty;
            },
            get savings() {
                const qty = parseInt(this.cantidad) || 1;
                return (precioUnitario * qty) - this.totalPrice;
            },
            get buttonText() {
                return `Pagar ${this.formatPrice(this.totalPrice)} con Helppiu Pay`;
            },
            formatPrice(val) {
                return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(val);
            },
        };
    }
</script>
@endpush

</x-app-layout>