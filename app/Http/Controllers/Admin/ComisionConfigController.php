<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComisionConfig;
use App\Models\Sorteo;
use Illuminate\Http\Request;

class ComisionConfigController extends Controller
{
    public function index(Request $request)
    {
        $sorteoId = $request->query('sorteo');
        $sorteoFiltro = $sorteoId ? Sorteo::find($sorteoId) : null;

        // Configs globales (sorteo_id = null)
        $globales = ComisionConfig::whereNull('sorteo_id')->get()->keyBy(fn($c) => "{$c->rol}-{$c->canal}");

        // Configs específicas del sorteo seleccionado
        $especificas = $sorteoId
            ? ComisionConfig::where('sorteo_id', $sorteoId)->get()->keyBy(fn($c) => "{$c->rol}-{$c->canal}")
            : collect();

        $sorteos = Sorteo::orderBy('fecha_sorteo', 'desc')->get();

        // Combinaciones válidas de rol-canal
        $combinaciones = [
            ['rol' => 'comercial', 'canal' => 'directo', 'label' => 'Comercial - Venta directa'],
            ['rol' => 'lider', 'canal' => 'directo', 'label' => 'Líder - Venta directa'],
            ['rol' => 'lider', 'canal' => 'override', 'label' => 'Líder - Override (de sus comerciales)'],
            ['rol' => 'coordinador', 'canal' => 'override', 'label' => 'Coordinador - Override (de sus líderes)'],
        ];

        return view('admin.comisiones.config', compact('globales', 'especificas', 'sorteos', 'sorteoFiltro', 'combinaciones'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'configs' => ['required', 'array'],
            'configs.*.rol' => ['required', 'in:comercial,lider,coordinador'],
            'configs.*.canal' => ['required', 'in:directo,override'],
            'configs.*.tipo' => ['required', 'in:porcentaje,fijo,meta'],
            'configs.*.valor' => ['required', 'numeric', 'min:0'],
            'configs.*.activo' => ['nullable', 'boolean'],
            'sorteo_id' => ['nullable', 'exists:sorteos,id'],
        ]);

        foreach ($validated['configs'] as $config) {
            ComisionConfig::updateOrCreate(
                [
                    'sorteo_id' => $validated['sorteo_id'] ?? null,
                    'rol' => $config['rol'],
                    'canal' => $config['canal'],
                ],
                [
                    'tipo' => $config['tipo'],
                    'valor' => $config['valor'],
                ]
            );
        }

        $mensaje = empty($validated['sorteo_id']) 
            ? 'Configuración global de comisiones actualizada.' 
            : 'Configuración de comisiones para el sorteo actualizada.';

        return back()->with('success', $mensaje);
    }
}
