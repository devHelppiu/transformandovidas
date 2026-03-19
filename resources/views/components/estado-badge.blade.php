@props(['estado'])

@php
$colors = match($estado) {
    'pagado', 'verificado', 'activo', 'pagada' => 'bg-green-100 text-green-800',
    'reservado', 'pendiente', 'borrador' => 'bg-yellow-100 text-yellow-800',
    'anulado', 'rechazado' => 'bg-red-100 text-red-800',
    'cerrado' => 'bg-blue-100 text-blue-800',
    'ejecutado' => 'bg-purple-100 text-purple-800',
    default => 'bg-gray-100 text-gray-800',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors }}">
    {{ ucfirst($estado) }}
</span>