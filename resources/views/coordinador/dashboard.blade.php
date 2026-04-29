<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Coordinador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header con saludo --}}
            <div class="bg-gradient-to-r from-tv-blue to-purple-600 rounded-2xl p-6 text-white">
                <h1 class="font-urbanist font-bold text-2xl">Hola, {{ auth()->user()->name }}</h1>
                <p class="text-white/70 mt-1">
                    {{ $totalLideres }} {{ $totalLideres === 1 ? 'Líder' : 'Líderes' }} · 
                    {{ $totalComerciales }} {{ $totalComerciales === 1 ? 'Comercial' : 'Comerciales' }}
                </p>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión acumulada</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($comisionesAcumuladas, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Pendiente por liquidar</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($comisionesPendientes, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión pagada</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">${{ number_format($comisionesPagadas, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Mis Líderes --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-urbanist font-bold text-gray-900">Mis Líderes</h3>
                    <a href="{{ route('coordinador.lideres.create') }}" 
                       class="bg-tv-blue text-white px-4 py-2 rounded-lg font-urbanist text-sm font-semibold hover:bg-tv-blue/90 transition">
                        + Crear Líder
                    </a>
                </div>
                
                @if($lideres->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">Aún no tienes líderes. ¡Crea el primero!</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($lideres as $lider)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-tv-bg flex items-center justify-center">
                                        <span class="font-urbanist font-bold text-tv-blue">{{ substr($lider->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-urbanist font-semibold text-gray-900">{{ $lider->user->name }}</p>
                                        <p class="font-urbanist text-sm text-gray-500">
                                            <span class="font-mono">{{ $lider->codigo_ref }}</span> · 
                                            {{ $lider->comerciales_count }} comerciales
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$lider->is_active)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-urbanist">Inactivo</span>
                                    @endif
                                    <a href="{{ route('coordinador.lideres.edit', $lider) }}" 
                                       class="text-gray-400 hover:text-tv-blue transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Acciones rápidas --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('coordinador.lideres.index') }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg font-urbanist text-sm text-gray-700 hover:border-tv-blue hover:text-tv-blue transition">
                    Ver todos los líderes
                </a>
                <a href="{{ route('coordinador.comisiones.index') }}" 
                   class="px-4 py-2 bg-white border border-gray-200 rounded-lg font-urbanist text-sm text-gray-700 hover:border-tv-blue hover:text-tv-blue transition">
                    Ver mis comisiones
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
