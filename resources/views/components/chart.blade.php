{{-- 
    Componente x-chart
    
    Uso:
    <x-chart 
        id="myChart" 
        :options="$chartOptions" 
        :series="$chartSeries" 
        type="bar"
        height="350"
    />
    
    O con data simple:
    <x-chart 
        id="ventasChart"
        :labels="['Ene', 'Feb', 'Mar']"
        :data="[30, 40, 25]"
        type="line"
    />
--}}

@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'bar',
    'height' => 350,
    'options' => [],
    'series' => [],
    'labels' => [],
    'data' => [],
    'colors' => ['#2227f5', '#e838bf', '#000EBF'],
])

@php
    // Si se pasa data simple, construir la serie
    $chartSeries = $series;
    if (empty($chartSeries) && !empty($data)) {
        $chartSeries = [['name' => 'Datos', 'data' => $data]];
    }
    
    // Configuración base
    $defaultOptions = [
        'chart' => [
            'type' => $type,
            'height' => $height,
            'toolbar' => ['show' => false],
            'fontFamily' => 'Urbanist, sans-serif',
        ],
        'colors' => $colors,
        'dataLabels' => ['enabled' => false],
        'stroke' => ['curve' => 'smooth', 'width' => 2],
        'xaxis' => ['categories' => $labels],
        'grid' => ['borderColor' => '#e5e7eb'],
        'tooltip' => ['theme' => 'light'],
    ];
    
    // Merge con opciones personalizadas
    $finalOptions = array_replace_recursive($defaultOptions, $options);
@endphp

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'w-full']) }}></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = @json($finalOptions);
        options.series = @json($chartSeries);
        
        var chart = new ApexCharts(document.querySelector("#{{ $id }}"), options);
        chart.render();
    });
</script>
@endpush
