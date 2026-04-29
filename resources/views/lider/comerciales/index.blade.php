<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis Comerciales</h2>
            <a href="{{ route('lider.comerciales.create') }}" 
               class="bg-tv-pink text-white px-4 py-2 rounded-lg font-urbanist text-sm font-semibold hover:bg-tv-pink/90 transition">
                + Crear Comercial
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if($comerciales->isEmpty())
                    <div class="p-10 text-center">
                        <p class="font-urbanist text-gray-500">Aún no tienes comerciales. ¡Crea el primero!</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left font-urbanist text-xs font-semibold text-gray-500 uppercase">Código</th>
                                    <th class="px-6 py-3 text-center font-urbanist text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($comerciales as $comercial)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-urbanist font-semibold text-gray-900">{{ $comercial->user->name }}</td>
                                        <td class="px-6 py-4 font-urbanist text-sm text-gray-500">{{ $comercial->user->email }}</td>
                                        <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $comercial->codigo_ref }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($comercial->is_active)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-urbanist">Activo</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-urbanist">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('lider.comerciales.edit', $comercial) }}" 
                                               class="text-tv-blue hover:underline font-urbanist text-sm">Editar</a>
                                            <form action="{{ route('lider.comerciales.toggle-active', $comercial) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-gray-500 hover:underline font-urbanist text-sm">
                                                    {{ $comercial->is_active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
