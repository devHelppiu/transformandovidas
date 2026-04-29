<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Coordinadores</h2>
            <a href="{{ route('admin.coordinadores.create') }}" 
               class="bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                + Nuevo Coordinador
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if($coordinadores->isEmpty())
                    <div class="p-10 text-center">
                        <div class="w-16 h-16 bg-tv-bg rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-tv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="font-urbanist text-gray-500 mb-4">No hay coordinadores registrados.</p>
                        <a href="{{ route('admin.coordinadores.create') }}" 
                           class="inline-flex items-center gap-2 bg-tv-blue hover:bg-tv-blue/90 text-white font-urbanist font-bold text-sm px-5 py-2.5 rounded-xl transition-colors">
                            Crear primer coordinador
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Coordinador</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Líderes</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right font-urbanist text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($coordinadores as $coordinador)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-tv-bg flex items-center justify-center">
                                                    <span class="font-urbanist font-bold text-tv-blue">
                                                        {{ substr($coordinador->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-urbanist font-semibold text-gray-900">{{ $coordinador->user->name }}</p>
                                                    <p class="font-urbanist text-sm text-gray-500">{{ $coordinador->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="font-urbanist font-bold text-tv-blue-dark">{{ $coordinador->lideres_count }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($coordinador->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-urbanist">Activo</span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-urbanist">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.coordinadores.show', $coordinador) }}" 
                                                   class="text-gray-500 hover:text-tv-blue transition-colors" title="Ver detalle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.coordinadores.edit', $coordinador) }}" 
                                                   class="text-gray-500 hover:text-tv-blue transition-colors" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.coordinadores.toggle-active', $coordinador) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="{{ $coordinador->is_active ? 'text-amber-500 hover:text-amber-600' : 'text-green-500 hover:text-green-600' }} transition-colors"
                                                            title="{{ $coordinador->is_active ? 'Desactivar' : 'Activar' }}">
                                                        @if($coordinador->is_active)
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $coordinadores->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
