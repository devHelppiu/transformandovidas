<x-cliente-layout>

    {{-- Mapeo de estados del pago a estilos del badge (Figma node 25:2378) --}}
    @php
        $badgeStyles = [
            'verificado' => ['bg' => 'bg-[#93ff93]', 'text' => 'text-[#035c0c]', 'label' => 'Pago verificado'],
            'pendiente'  => ['bg' => 'bg-[#ffd693]', 'text' => 'text-[#b65a09]', 'label' => 'Pago pendiente'],
            'rechazado'  => ['bg' => 'bg-[#ffd2d2]', 'text' => 'text-[#c01a1a]', 'label' => 'Pago rechazado'],
        ];
    @endphp

    <div class="bg-[#f9fafb] py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-flash-messages />

            {{-- Encabezado: título + buscador --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
                <h1 class="font-urbanist font-extrabold text-[#2f2f2f] uppercase tracking-tight"
                    style="font-size: clamp(1.5rem, 3vw, 2rem);">
                    Mis tickets
                </h1>

                <form method="GET" action="{{ route('cliente.tickets.index') }}"
                      class="bg-tv-bg flex items-center justify-between gap-3 px-6 py-2.5 rounded-3xl w-full sm:w-72">
                    <input type="text"
                           name="q"
                           value="{{ $search }}"
                           placeholder="Buscar por sorteo"
                           class="bg-transparent border-0 p-0 focus:ring-0 font-urbanist text-sm text-[#2f2f2f] placeholder-[#2f2f2f]/60 w-full">
                    <button type="submit" class="text-[#2f2f2f] hover:text-tv-blue-dark transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            @if($compras->count())
                <div class="flex flex-col gap-3.5">
                    @foreach($compras as $compra)
                        @php
                            $badge = $badgeStyles[$compra->estado_pago] ?? $badgeStyles['pendiente'];
                        @endphp

                        <article class="bg-white border border-[#e8ebff] rounded-2xl px-6 py-4 flex flex-col gap-4">
                            {{-- Bloque superior: imagen + info izquierda + info derecha + badge --}}
                            <div class="flex flex-col md:flex-row md:items-center gap-4">
                                {{-- Imagen del sorteo --}}
                                @if($compra->sorteo->imagen)
                                    <img src="{{ Storage::url($compra->sorteo->imagen) }}"
                                         alt="{{ $compra->sorteo->nombre }}"
                                         class="w-[118px] h-[118px] rounded-2xl object-cover flex-shrink-0">
                                @else
                                    <div class="w-[118px] h-[118px] rounded-2xl bg-tv-bg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-12 h-12 text-tv-blue/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Info principal: dos columnas + badge --}}
                                <div class="flex-1 flex flex-col gap-4">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                                        {{-- Columna izquierda: fecha sorteo, nombre, premio --}}
                                        <div class="flex flex-col gap-2 md:w-[290px]">
                                            <p class="font-urbanist text-sm text-[#2f2f2f]">
                                                Fecha del sorteo: {{ $compra->sorteo->fecha_sorteo->format('d/m/Y') }}
                                            </p>
                                            <p class="font-urbanist font-extrabold text-[#2f2f2f] text-xl uppercase leading-tight">
                                                {{ $compra->sorteo->nombre }}
                                            </p>
                                            <div class="flex items-center gap-2.5">
                                                <span class="font-urbanist text-sm text-[#2f2f2f]">Premio</span>
                                                <span class="font-urbanist font-bold text-tv-pink text-base leading-none">
                                                    ${{ number_format($compra->sorteo->valor_premio ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Columna derecha: detalles compra --}}
                                        <div class="flex flex-col gap-2 md:w-[288px]">
                                            <div class="flex items-center justify-between gap-2.5 font-urbanist text-sm text-[#2f2f2f]">
                                                <span>Fecha de la compra:</span>
                                                <span>{{ $compra->fecha_compra->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex items-center justify-between font-urbanist text-sm">
                                                <span class="text-[#2f2f2f]">Valor del ticket</span>
                                                <span class="font-bold text-tv-blue-dark">
                                                    ${{ number_format($compra->precio_unitario, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between font-urbanist text-sm">
                                                <span class="text-[#2f2f2f]">Cantidad adquirida</span>
                                                <span class="font-bold text-tv-blue-dark">{{ $compra->cantidad }}X</span>
                                            </div>
                                            <div class="flex items-center justify-between font-urbanist">
                                                <span class="text-sm text-[#2f2f2f]">Total</span>
                                                <span class="font-bold text-tv-blue-dark text-base">
                                                    ${{ number_format($compra->total, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Badge estado pago --}}
                                    <div class="flex">
                                        <span class="{{ $badge['bg'] }} {{ $badge['text'] }} font-urbanist text-sm rounded-lg px-2 py-0.5">
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Separador --}}
                            <hr class="border-t border-[#e8ebff]">

                            {{-- Números de tickets adquiridos --}}
                            <div class="flex flex-col gap-2">
                                <p class="font-urbanist text-sm text-[#2f2f2f]">N° Tickets adquiridos</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($compra->tickets as $ticket)
                                        <a href="{{ route('cliente.tickets.show', $ticket) }}"
                                           class="bg-tv-bg text-tv-blue-dark font-urbanist font-bold text-xl rounded-3xl px-6 py-2 hover:bg-tv-blue-dark hover:text-white transition-colors">
                                            {{ $ticket->numero }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Paginación: "Mostrando X de Y" + links --}}
                <div class="flex flex-col sm:flex-row items-center justify-end gap-6 mt-10">
                    <p class="font-montserrat text-sm text-[#1d1d1d]">
                        Mostrando {{ $compras->count() }} de {{ $compras->total() }}
                    </p>
                    <div>
                        {{ $compras->onEachSide(1)->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white border border-[#e8ebff] rounded-2xl py-16 text-center">
                    <div class="w-16 h-16 bg-tv-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <p class="font-urbanist text-[#2f2f2f]">
                        @if($search !== '')
                            No encontramos compras que coincidan con "{{ $search }}".
                        @else
                            Aún no tienes tickets comprados.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-cliente-layout>
