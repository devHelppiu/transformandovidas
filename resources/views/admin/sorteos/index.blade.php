<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sorteos</h2>
            <a href="{{ route('admin.sorteos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                Crear Sorteo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Sorteo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Premio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sorteos as $sorteo)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sorteo->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sorteo->fecha_sorteo->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ '$' . number_format($sorteo->precio_ticket, 0, ',', '.') }} COP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ '$' . number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }} COP</td>
                                <td class="px-6 py-4 whitespace-nowrap"><x-estado-badge :estado="$sorteo->estado" /></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.sorteos.show', $sorteo) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                    @if($sorteo->estado === 'borrador')
                                        <a href="{{ route('admin.sorteos.edit', $sorteo) }}" class="ml-3 text-gray-600 hover:text-gray-900">Editar</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay sorteos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $sorteos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>