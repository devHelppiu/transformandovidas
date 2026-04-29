<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle Coordinador</h2>
            <a href="{{ route('admin.coordinadores.edit', $coordinador) }}" 
               class="bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Header con info del coordinador --}}
            <div class="bg-gradient-to-r from-tv-blue to-purple-600 rounded-2xl p-8 text-white">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="font-urbanist font-bold text-3xl">{{ substr($coordinador->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="font-urbanist font-bold text-2xl">{{ $coordinador->user->name }}</h1>
                        <p class="text-white/80">{{ $coordinador->user->email }}</p>
                        @if($coordinador->user->phone)
                            <p class="text-white/60 text-sm">{{ $coordinador->user->phone }}</p>
                        @endif
                    </div>
                    <div class="ml-auto">
                        @if($coordinador->is_active)
                            <span class="px-3 py-1 bg-white/20 text-white text-sm rounded-full font-urbanist">Activo</span>
                        @else
                            <span class="px-3 py-1 bg-red-500/50 text-white text-sm rounded-full font-urbanist">Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Líderes totales</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">{{ $stats['lideres_count'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Líderes activos</p>
                    <p class="font-urbanist font-bold text-2xl text-green-600 mt-1">{{ $stats['lideres_activos'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comerciales en rama</p>
                    <p class="font-urbanist font-bold text-2xl text-tv-blue mt-1">{{ $stats['comerciales_count'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión acumulada</p>
                    <p class="font-urbanist font-bold text-2xl text-gray-900 mt-1">${{ number_format($stats['comision_acumulada'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                    <p class="font-urbanist text-sm text-gray-500">Comisión pendiente</p>
                    <p class="font-urbanist font-bold text-2xl text-amber-600 mt-1">${{ number_format($stats['comision_pendiente'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Lista de líderes --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-urbanist font-bold text-gray-900">Líderes del coordinador</h3>
                </div>

                @if($coordinador->lideres->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">Este coordinador no tiene líderes asignados.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Líder</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Comerciales</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($coordinador->lideres as $lider)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-urbanist font-semibold text-gray-900">{{ $lider->user->name }}</p>
                                                <p class="font-urbanist text-sm text-gray-500">{{ $lider->user->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-sm text-tv-blue">{{ $lider->codigo_ref }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-urbanist font-bold text-tv-blue-dark">{{ $lider->comerciales->count() }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($lider->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-urbanist">Activo</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-urbanist">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="flex">
                <a href="{{ route('admin.coordinadores.index') }}" class="font-urbanist text-sm text-gray-600 hover:text-gray-900">
                    ← Volver a coordinadores
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
