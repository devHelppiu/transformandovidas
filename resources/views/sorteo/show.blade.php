<x-public-layout>
    <x-slot name="title">{{ $sorteo->nombre }} — {{ config('app.name') }}</x-slot>

    <div class="bg-tv-bg min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-flash-messages />

            @if($sorteo->estado === 'ejecutado')
                <div class="bg-white border border-gray-100 rounded-2xl p-10 text-center shadow-sm">
                    <p class="font-urbanist font-semibold text-gray-700 text-lg mb-2">Este sorteo ya fue ejecutado</p>
                    <p class="font-urbanist text-4xl font-black text-tv-blue-dark mt-3">
                        Número ganador: {{ $sorteo->numero_ganador }}
                    </p>
                </div>

            @elseif($sorteo->estado !== 'activo')
                <div class="bg-white border border-gray-100 rounded-2xl p-10 text-center shadow-sm">
                    <p class="font-urbanist text-gray-500 text-lg">
                        @if($sorteo->estado === 'cerrado')
                            Las ventas están cerradas. El sorteo se ejecutará pronto.
                        @else
                            Este sorteo aún no está activo.
                        @endif
                    </p>
                </div>

            @else
                <div x-data="cart()">
                    {{-- Tarjeta gradiente full-width con info del sorteo --}}
                    @php $pctHeader = $sorteo->total_tickets > 0 ? round(($ticketsVendidos / $sorteo->total_tickets) * 100, 1) : 0; @endphp
                    <div class="relative bg-gradient-to-r from-tv-blue via-purple-600 to-tv-pink rounded-2xl p-6 md:p-8 mb-8 shadow-xl overflow-hidden">
                        @if($sorteo->imagen)
                            <div class="absolute inset-0 opacity-20 pointer-events-none">
                                <img src="{{ asset('storage/' . $sorteo->imagen) }}"
                                     alt=""
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div>
                                <div class="flex items-center gap-2 text-white/70 text-sm mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Sorteo: {{ $sorteo->fecha_sorteo->format('d \\d\\e F \\d\\e Y') }}
                                </div>
                                <h1 class="font-urbanist font-bold text-white text-3xl md:text-5xl uppercase leading-tight">
                                    {{ $sorteo->nombre }}
                                </h1>
                            </div>
                            <div class="text-left md:text-right">
                                <p class="font-urbanist text-white/60 text-xs uppercase tracking-wider">Premio</p>
                                <p class="font-urbanist font-black text-white text-3xl md:text-4xl">${{ number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="flex justify-between font-urbanist text-xs text-white/70 mb-2">
                                <span>{{ $pctHeader }}% Vendidos</span>
                                <span>{{ number_format($ticketsVendidos, 0, ',', '.') }} / {{ number_format($sorteo->total_tickets, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-white/20 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full bg-white" style="width: {{ max($pctHeader, 1) }}%"></div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3 mb-8">
                        <div class="flex items-center gap-2">
                            <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all"
                                  :class="step > 1 ? 'bg-green-500 text-white' : step === 1 ? 'bg-tv-blue text-white shadow-lg' : 'bg-gray-200 text-gray-400'">
                                <template x-if="step > 1"><span>&#10003;</span></template>
                                <template x-if="step <= 1"><span>1</span></template>
                            </span>
                            <span class="font-urbanist text-sm font-medium hidden sm:inline"
                                  :class="step === 1 ? 'text-tv-blue-dark' : 'text-gray-400'">Tickets</span>
                        </div>
                        <div class="w-8 sm:w-16 h-0.5 rounded" :class="step > 1 ? 'bg-green-400' : 'bg-gray-200'"></div>
                        <div class="flex items-center gap-2">
                            <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all"
                                  :class="step === 2 ? 'bg-tv-blue text-white shadow-lg' : 'bg-gray-200 text-gray-400'">2</span>
                            <span class="font-urbanist text-sm font-medium hidden sm:inline"
                                  :class="step === 2 ? 'text-tv-blue-dark' : 'text-gray-400'">Datos y pago</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('tickets.comprar', $sorteo) }}">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                            <div class="lg:col-span-2 space-y-5">

                                {{-- PASO 1 --}}
                                <div x-show="step === 1"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0">
                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-10 h-10 bg-tv-bg rounded-xl flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-urbanist font-bold text-gray-900">Selecciona tus tickets</h3>
                                                <p class="font-urbanist text-xs text-gray-400">Números asignados automáticamente al azar</p>
                                            </div>
                                        </div>

                                        <div class="mb-6">
                                            <label class="block font-urbanist text-sm font-medium text-gray-700 mb-2">¿Cuántos tickets quieres?</label>
                                            @if($sorteo->compra_minima > 1)
                                                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-3 font-urbanist text-sm text-amber-700">
                                                    ⚡ Compra mínima: <strong>{{ $sorteo->compra_minima }} tickets</strong>
                                                </div>
                                            @endif
                                            <div class="flex items-center justify-center gap-5 py-2">
                                                <button type="button" @click="decrementQty()"
                                                        class="w-12 h-12 rounded-xl border-2 text-xl font-bold transition-all flex items-center justify-center"
                                                        :class="parseInt(cantidad) <= minQty ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-tv-blue/30 text-tv-blue hover:bg-tv-bg hover:border-tv-blue active:scale-95'">−</button>
                                                <div class="text-center">
                                                    <span class="font-urbanist font-black text-gray-700 text-4xl" x-text="cantidad"></span>
                                                    <p class="font-urbanist text-xs text-gray-400 mt-1" x-text="parseInt(cantidad) === 1 ? 'ticket' : 'tickets'"></p>
                                                </div>
                                                <button type="button" @click="incrementQty()"
                                                        class="w-12 h-12 rounded-xl border-2 border-tv-blue/30 text-xl font-bold text-tv-blue hover:bg-tv-bg hover:border-tv-blue transition-all flex items-center justify-center active:scale-95">+</button>
                                            </div>
                                        </div>

                                        {{-- Buscador de números / Number Picker --}}
                                        <div class="border-t border-gray-100 pt-5 mb-5">
                                            <div class="flex items-center justify-between mb-4">
                                                <p class="font-urbanist text-sm font-semibold text-gray-700">¿Cómo quieres tus números?</p>
                                            </div>
                                            
                                            {{-- Toggle Aleatorio / Elegir --}}
                                            <div class="flex rounded-xl bg-gray-100 p-1 mb-4">
                                                <button type="button" 
                                                        @click="pickerModo = 'aleatorio'; numerosSeleccionados = []"
                                                        class="flex-1 py-2 px-4 rounded-lg font-urbanist text-sm font-medium transition-all"
                                                        :class="pickerModo === 'aleatorio' ? 'bg-white text-tv-blue shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                                    🎲 Aleatorio
                                                </button>
                                                <button type="button" 
                                                        @click="pickerModo = 'manual'; cargarNumerosPicker()"
                                                        class="flex-1 py-2 px-4 rounded-lg font-urbanist text-sm font-medium transition-all"
                                                        :class="pickerModo === 'manual' ? 'bg-white text-tv-blue shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                                    ✋ Elegir números
                                                </button>
                                            </div>

                                            {{-- Grid de números (solo visible en modo manual) --}}
                                            <div x-show="pickerModo === 'manual'" x-cloak class="space-y-4">
                                                {{-- Controles de navegación --}}
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <select x-model="pickerRango" @change="cargarNumerosPicker()"
                                                            class="rounded-lg border-gray-200 text-sm font-urbanist py-2 pr-8">
                                                        <template x-for="rango in pickerRangos" :key="rango.from">
                                                            <option :value="rango.from" x-text="rango.label"></option>
                                                        </template>
                                                    </select>
                                                    
                                                    <div class="flex-1 relative">
                                                        <input type="text" 
                                                               x-model="pickerBusqueda"
                                                               @input.debounce.300ms="buscarNumeroPicker()"
                                                               placeholder="Buscar: 1234"
                                                               maxlength="4"
                                                               class="w-full rounded-lg border-gray-200 text-sm font-urbanist py-2 px-3 font-mono">
                                                    </div>
                                                </div>

                                                {{-- Seleccionados counter --}}
                                                <div class="flex items-center justify-between text-sm font-urbanist">
                                                    <span class="text-gray-500">
                                                        Seleccionados: <strong class="text-tv-blue" x-text="numerosSeleccionados.length"></strong> / <span x-text="cantidad"></span>
                                                    </span>
                                                    <button type="button" 
                                                            x-show="numerosSeleccionados.length > 0"
                                                            @click="numerosSeleccionados = []"
                                                            class="text-red-500 hover:text-red-600 text-xs">
                                                        Limpiar
                                                    </button>
                                                </div>

                                                {{-- Grid de números (compacto, max-h con scroll si hace falta) --}}
                                                <div class="relative">
                                                    <div x-show="pickerCargando" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 rounded-xl">
                                                        <svg class="animate-spin h-6 w-6 text-tv-blue" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-1 max-h-[260px] overflow-y-auto p-1 rounded-xl bg-gray-50/40">
                                                        <template x-for="num in pickerNumeros" :key="num.numero">
                                                            <button type="button"
                                                                    @click="toggleNumeroPicker(num)"
                                                                    :disabled="num.estado !== 'disponible' && !numerosSeleccionados.includes(num.numero)"
                                                                    class="h-9 rounded-md text-[11px] font-mono font-bold transition-all flex items-center justify-center"
                                                                    :class="{
                                                                        'bg-tv-pink text-white shadow-sm': numerosSeleccionados.includes(num.numero),
                                                                        'bg-white border border-gray-200 text-gray-700 hover:border-tv-blue hover:bg-tv-bg': num.estado === 'disponible' && !numerosSeleccionados.includes(num.numero),
                                                                        'bg-gray-100 text-gray-300 cursor-not-allowed': num.estado === 'reservado',
                                                                        'bg-gray-200 text-gray-400 cursor-not-allowed line-through': num.estado === 'vendido',
                                                                    }">
                                                                <span x-text="num.numero"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Paginación --}}
                                                <div class="flex items-center justify-between">
                                                    <button type="button" 
                                                            @click="pickerPaginaAnterior()"
                                                            :disabled="parseInt(pickerRango) === 0"
                                                            class="px-3 py-1.5 rounded-lg text-sm font-urbanist transition-all"
                                                            :class="parseInt(pickerRango) === 0 ? 'text-gray-300 cursor-not-allowed' : 'text-tv-blue hover:bg-tv-bg'">
                                                        ← Anterior
                                                    </button>
                                                    <span class="text-xs text-gray-400 font-urbanist">
                                                        <span x-text="pickerDisponiblesGlobal"></span> disponibles
                                                    </span>
                                                    <button type="button" 
                                                            @click="pickerPaginaSiguiente()"
                                                            :disabled="parseInt(pickerRango) + 50 >= {{ $sorteo->total_tickets }}"
                                                            class="px-3 py-1.5 rounded-lg text-sm font-urbanist transition-all"
                                                            :class="parseInt(pickerRango) + 50 >= {{ $sorteo->total_tickets }} ? 'text-gray-300 cursor-not-allowed' : 'text-tv-blue hover:bg-tv-bg'">
                                                        Siguiente →
                                                    </button>
                                                </div>

                                                {{-- Completar al azar --}}
                                                <div x-show="numerosSeleccionados.length > 0 && numerosSeleccionados.length < parseInt(cantidad)" 
                                                     class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 font-urbanist text-sm text-amber-700">
                                                    Te faltan <strong x-text="parseInt(cantidad) - numerosSeleccionados.length"></strong> números.
                                                    <button type="button" 
                                                            @click="completarAlAzar()"
                                                            class="underline hover:no-underline ml-1">
                                                        Completar al azar
                                                    </button>
                                                </div>

                                                {{-- Números seleccionados preview --}}
                                                <div x-show="numerosSeleccionados.length > 0" class="flex flex-wrap gap-1.5">
                                                    <template x-for="num in numerosSeleccionados" :key="num">
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-tv-pink/10 text-tv-pink text-xs font-mono font-bold">
                                                            <span x-text="num"></span>
                                                            <button type="button" @click="numerosSeleccionados = numerosSeleccionados.filter(n => n !== num)" class="hover:text-red-500">×</button>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Hidden inputs para enviar los números --}}
                                            <template x-if="pickerModo === 'manual' && numerosSeleccionados.length > 0">
                                                <template x-for="num in numerosSeleccionados" :key="'input-' + num">
                                                    <input type="hidden" name="numeros[]" :value="num">
                                                </template>
                                            </template>

                                            {{-- Mensaje modo aleatorio --}}
                                            <p x-show="pickerModo === 'aleatorio'" class="font-urbanist text-xs text-gray-400 text-center">
                                                Los números se asignarán al azar al completar la compra
                                            </p>
                                        </div>

                                        @if($combos->where('activo', true)->isNotEmpty())
                                            <div class="border-t border-gray-100 pt-5">
                                                <p class="font-urbanist text-sm font-semibold text-gray-700 mb-3">🏷️ Combos con descuento</p>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                    @foreach($combos as $combo)
                                                        @if($combo->activo && $combo->cantidad >= $sorteo->compra_minima)
                                                            <div class="cursor-pointer" @click="selectCombo('{{ $combo->cantidad }}', '{{ $combo->id }}', {{ $combo->precio }})">
                                                                <div class="relative rounded-2xl p-4 text-center transition-all border-2"
                                                                     :class="comboId === '{{ $combo->id }}' ? 'border-tv-blue bg-tv-bg shadow-md scale-[1.02]' : 'border-gray-100 bg-white hover:border-tv-blue/30 hover:shadow-sm'">
                                                                    @php $desc = $combo->descuento((float) $sorteo->precio_ticket); @endphp
                                                                    @if($desc > 0)
                                                                        <span class="absolute -top-2 -right-2 text-[10px] font-bold bg-green-500 text-white px-2 py-0.5 rounded-full">-{{ $desc }}%</span>
                                                                    @endif
                                                                    <div class="font-urbanist font-black text-gray-800 text-3xl" :class="comboId === '{{ $combo->id }}' && 'text-tv-blue'">{{ $combo->cantidad }}</div>
                                                                    <div class="font-urbanist text-xs text-gray-400 mt-0.5">tickets</div>
                                                                    <div class="font-urbanist font-bold text-base mt-2" :class="comboId === '{{ $combo->id }}' ? 'text-tv-blue' : 'text-gray-700'">${{ number_format($combo->precio, 0, ',', '.') }}</div>
                                                                    <div class="font-urbanist text-[10px] text-gray-400 mt-1">${{ number_format($combo->precio / $combo->cantidad, 0, ',', '.') }} c/u</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <x-input-error :messages="$errors->get('cantidad')" class="mt-3" />
                                        <x-input-error :messages="$errors->get('combo_id')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('numeros')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('numeros.*')" class="mt-2" />
                                        <input type="hidden" name="cantidad" x-model="cantidad">
                                        <input type="hidden" name="combo_id" x-model="comboId">

                                        <button type="button" @click="if(selected) step = 2"
                                                class="mt-6 w-full py-3.5 rounded-xl font-urbanist font-bold text-sm transition-all"
                                                :class="selected ? 'bg-tv-blue text-white hover:bg-tv-blue/90 shadow-lg shadow-tv-blue/20 active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">
                                            Continuar
                                        </button>
                                    </div>
                                </div>

                                {{-- PASO 2 --}}
                                <div x-show="step === 2"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0">
                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-tv-bg rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="font-urbanist font-bold text-gray-900">Tus datos</h3>
                                                    <p class="font-urbanist text-xs text-gray-400">No necesitas crear cuenta</p>
                                                </div>
                                            </div>
                                            <button type="button" @click="step = 1"
                                                    class="font-urbanist text-sm font-medium text-gray-500 hover:text-tv-blue transition-colors">
                                                ← Atrás
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block font-urbanist text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
                                                <input type="text" name="nombre" x-model="nombre" value="{{ old('nombre') }}" required placeholder="Tu nombre completo"
                                                       class="w-full rounded-xl border-gray-200 focus:border-tv-blue focus:ring-tv-blue py-3 px-4 font-urbanist text-sm"/>
                                                <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block font-urbanist text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                                                    <input type="email" name="email" x-model="email" value="{{ old('email') }}" required placeholder="tu@correo.com"
                                                           class="w-full rounded-xl border-gray-200 focus:border-tv-blue focus:ring-tv-blue py-3 px-4 font-urbanist text-sm"/>
                                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                                </div>
                                                <div>
                                                    <label class="block font-urbanist text-sm font-medium text-gray-700 mb-1.5">Teléfono <span class="text-gray-400">(opcional)</span></label>
                                                    <input type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="300 123 4567"
                                                           class="w-full rounded-xl border-gray-200 focus:border-tv-blue focus:ring-tv-blue py-3 px-4 font-urbanist text-sm"/>
                                                    <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
                                                </div>
                                            </div>
                                            @if(!session('referral_comercial_id'))
                                                <div>
                                                    <label class="block font-urbanist text-sm font-medium text-gray-700 mb-1.5">Código de referido <span class="text-gray-400">(opcional)</span></label>
                                                    <input type="text" name="codigo_referido" value="{{ old('codigo_referido') }}" placeholder="TV-XXXXXX"
                                                           class="w-full rounded-xl border-gray-200 focus:border-tv-blue focus:ring-tv-blue py-3 px-4 font-urbanist text-sm"/>
                                                    <x-input-error :messages="$errors->get('codigo_referido')" class="mt-1" />
                                                </div>
                                            @endif
                                        </div>

                                        @if(session('referral_codigo'))
                                            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 flex items-center gap-2 font-urbanist text-sm text-green-700">
                                                ✓ Referido: <span class="font-mono font-bold">{{ session('referral_codigo') }}</span>
                                            </div>
                                        @endif

                                        <x-input-error :messages="$errors->get('sorteo')" class="mt-2" />
                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT: Resumen --}}
                            <div class="lg:col-span-1">
                                <div class="sticky top-24 space-y-4">
                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                                        <h4 class="font-urbanist font-bold text-gray-900 mb-4">Resumen de compra</h4>
                                        <div x-show="!selected" class="text-center py-4">
                                            <p class="font-urbanist text-sm text-gray-400">Selecciona tickets para ver el total</p>
                                        </div>
                                        <div x-show="selected" class="space-y-3">
                                            <div class="flex justify-between font-urbanist text-sm">
                                                <span class="text-gray-500">Tickets</span>
                                                <span class="font-medium" x-text="(parseInt(cantidad) || 1) + 'x'"></span>
                                            </div>
                                            <div class="flex justify-between font-urbanist text-sm">
                                                <span class="text-gray-500">Precio unitario</span>
                                                <span class="font-medium" x-text="formatPrice(unitPrice)"></span>
                                            </div>
                                            <template x-if="savings > 0">
                                                <div class="flex justify-between font-urbanist text-sm">
                                                    <span class="text-green-600">Ahorro</span>
                                                    <span class="text-green-600 font-medium" x-text="'-' + formatPrice(savings)"></span>
                                                </div>
                                            </template>
                                            <div class="border-t border-dashed pt-3 flex justify-between">
                                                <span class="font-urbanist font-bold text-gray-900">Total</span>
                                                <span class="font-urbanist font-black text-lg text-tv-blue" x-text="formatPrice(totalPrice)"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 font-urbanist text-xs text-gray-400">
                                        <span>🔒 Helppiu Pay</span>
                                        <span>💳 Tarjetas · PSE · Nequi</span>
                                        <span>📧 Confirmación</span>
                                    </div>

                                    {{-- Botón Pagar (solo visible en paso 2) --}}
                                    <button type="submit" x-show="step === 2"
                                            class="w-full py-3.5 rounded-xl font-urbanist font-bold text-sm transition-all"
                                            :class="nombre.trim() && email.trim() ? 'bg-tv-blue text-white hover:bg-tv-blue/90 shadow-lg shadow-tv-blue/20 active:scale-[0.98]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                            :disabled="!(nombre.trim() && email.trim())">
                                        🔒 <span x-text="buttonText"></span>
                                    </button>
                                    <p x-show="step === 2" class="font-urbanist text-xs text-center text-gray-400">
                                        Serás redirigido a <strong>Helppiu Pay</strong> de forma segura.
                                    </p>
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
        const totalTickets = {{ $sorteo->total_tickets }};
        const combosMap = {
            @foreach($combos->where('activo', true) as $c)
                '{{ $c->id }}': { cantidad: {{ $c->cantidad }}, precio: {{ $c->precio }} },
            @endforeach
        };

        // Generar rangos para el picker (50 por página = grid más corto)
        const PAGE_SIZE = 50;
        const pickerRangos = [];
        for (let i = 0; i < totalTickets; i += PAGE_SIZE) {
            const to = Math.min(i + PAGE_SIZE - 1, totalTickets - 1);
            pickerRangos.push({
                from: i,
                to: to,
                label: `${String(i).padStart(4, '0')} - ${String(to).padStart(4, '0')}`
            });
        }

        return {
            step: 1,
            cantidad: Math.max(minCompra, 1),
            comboId: '',
            precioBase: precioUnitario,
            minQty: Math.max(minCompra, 1),
            nombre: '{{ old("nombre", "") }}',
            email: '{{ old("email", "") }}',

            // Number picker state
            pickerModo: 'aleatorio',
            pickerNumeros: [],
            numerosSeleccionados: [],
            pickerRango: 0,
            pickerBusqueda: '',
            pickerCargando: false,
            pickerDisponiblesGlobal: 0,
            pickerRangos: pickerRangos,

            incrementQty() { 
                this.cantidad = parseInt(this.cantidad) + 1; 
                this.comboId = ''; 
                // Reset selection if quantity changed
                if (this.pickerModo === 'manual') {
                    this.numerosSeleccionados = [];
                }
            },
            decrementQty() { 
                const c = parseInt(this.cantidad); 
                if (c > this.minQty) { 
                    this.cantidad = c - 1; 
                    this.comboId = ''; 
                    // Reset selection if quantity changed
                    if (this.pickerModo === 'manual') {
                        this.numerosSeleccionados = [];
                    }
                } 
            },
            selectCombo(qty, id) { 
                this.cantidad = parseInt(qty); 
                this.comboId = id; 
                // Reset selection if combo changed
                if (this.pickerModo === 'manual') {
                    this.numerosSeleccionados = [];
                }
            },

            get activeCombo() { return this.comboId ? combosMap[this.comboId] : null; },
            get totalPrice() { return this.activeCombo ? this.activeCombo.precio : precioUnitario * parseInt(this.cantidad); },
            get unitPrice() { return this.totalPrice / (parseInt(this.cantidad) || 1); },
            get savings() { return (precioUnitario * parseInt(this.cantidad)) - this.totalPrice; },
            get buttonText() { return `Pagar ${this.formatPrice(this.totalPrice)} con Helppiu Pay`; },
            formatPrice(val) { return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(val); },

            // Computed: is selection valid to continue?
            get selected() {
                if (this.pickerModo === 'aleatorio') {
                    return true;
                }
                // In manual mode, must have exactly the required amount
                return this.numerosSeleccionados.length === parseInt(this.cantidad);
            },

            // Number picker methods
            async cargarNumerosPicker() {
                if (this.pickerModo !== 'manual') return;
                
                this.pickerCargando = true;
                const from = parseInt(this.pickerRango);
                const to = Math.min(from + 99, totalTickets - 1);
                
                try {
                    const response = await fetch(`{{ route('sorteo.numeros', $sorteo) }}?from=${from}&to=${to}`);
                    const data = await response.json();
                    this.pickerNumeros = data.numeros;
                    this.pickerDisponiblesGlobal = data.total_disponibles_global;
                } catch (error) {
                    console.error('Error cargando números:', error);
                } finally {
                    this.pickerCargando = false;
                }
            },

            toggleNumeroPicker(num) {
                if (num.estado !== 'disponible' && !this.numerosSeleccionados.includes(num.numero)) return;
                
                if (this.numerosSeleccionados.includes(num.numero)) {
                    this.numerosSeleccionados = this.numerosSeleccionados.filter(n => n !== num.numero);
                } else if (this.numerosSeleccionados.length < parseInt(this.cantidad)) {
                    this.numerosSeleccionados.push(num.numero);
                }
            },

            async buscarNumeroPicker() {
                if (!this.pickerBusqueda || this.pickerBusqueda.length < 1) return;
                
                const numero = this.pickerBusqueda.padStart(4, '0');
                const numeroInt = parseInt(numero);
                
                if (numeroInt >= 0 && numeroInt < totalTickets) {
                    const nuevoRango = Math.floor(numeroInt / 100) * 100;
                    this.pickerRango = nuevoRango;
                    await this.cargarNumerosPicker();
                }
            },

            pickerPaginaAnterior() {
                const actual = parseInt(this.pickerRango);
                if (actual >= 100) {
                    this.pickerRango = actual - 100;
                    this.cargarNumerosPicker();
                }
            },

            pickerPaginaSiguiente() {
                const actual = parseInt(this.pickerRango);
                if (actual + 100 < totalTickets) {
                    this.pickerRango = actual + 100;
                    this.cargarNumerosPicker();
                }
            },

            completarAlAzar() {
                const faltan = parseInt(this.cantidad) - this.numerosSeleccionados.length;
                if (faltan <= 0) return;

                const disponiblesEnPagina = this.pickerNumeros
                    .filter(n => n.estado === 'disponible' && !this.numerosSeleccionados.includes(n.numero))
                    .map(n => n.numero);
                
                const shuffled = disponiblesEnPagina.sort(() => Math.random() - 0.5);
                const toAdd = shuffled.slice(0, faltan);
                
                this.numerosSeleccionados = [...this.numerosSeleccionados, ...toAdd];
            }
        };
    }

    function numeroBuscador() {
        return {
            numeroInput: '',
            buscando: false,
            resultado: null,
            async buscarNumero() {
                if (!this.numeroInput || this.buscando) return;
                
                this.buscando = true;
                this.resultado = null;
                
                try {
                    const response = await fetch(`{{ route('sorteo.verificar-numero', $sorteo) }}?numero=${encodeURIComponent(this.numeroInput)}`);
                    this.resultado = await response.json();
                } catch (error) {
                    this.resultado = {
                        disponible: false,
                        mensaje: 'Error al verificar. Intenta de nuevo.',
                    };
                } finally {
                    this.buscando = false;
                }
            }
        };
    }
</script>
@endpush

</x-public-layout>
