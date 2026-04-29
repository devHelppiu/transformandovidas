<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSorteoRequest;
use App\Http\Requests\UpdateSorteoRequest;
use App\Models\Sorteo;
use App\Services\SorteoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SorteoController extends Controller
{
    public function __construct(
        private SorteoService $sorteoService,
    ) {}

    public function index()
    {
        $sorteos = Sorteo::latest()->paginate(15);
        return view('admin.sorteos.index', compact('sorteos'));
    }

    public function create()
    {
        return view('admin.sorteos.create');
    }

    public function store(StoreSorteoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('sorteos', 'public');
        }

        Sorteo::create($data);

        return redirect()->route('admin.sorteos.index')
            ->with('success', 'Sorteo creado exitosamente.');
    }

    public function show(Sorteo $sorteo)
    {
        $sorteo->load(['tickets.user', 'tickets.pago', 'tickets.comercial', 'combos']);
        $stats = [
            'total' => $sorteo->tickets->count(),
            'pagados' => $sorteo->tickets->where('estado', 'pagado')->count(),
            'reservados' => $sorteo->tickets->where('estado', 'reservado')->count(),
            'anulados' => $sorteo->tickets->where('estado', 'anulado')->count(),
        ];
        return view('admin.sorteos.show', compact('sorteo', 'stats'));
    }

    public function edit(Sorteo $sorteo)
    {
        return view('admin.sorteos.edit', compact('sorteo'));
    }

    public function update(UpdateSorteoRequest $request, Sorteo $sorteo): RedirectResponse
    {
        $data = $request->validated();
        unset($data['eliminar_imagen']);

        // Reemplazar imagen
        if ($request->hasFile('imagen')) {
            if ($sorteo->imagen) {
                Storage::disk('public')->delete($sorteo->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('sorteos', 'public');
        }
        // Eliminar imagen sin reemplazo
        elseif ($request->boolean('eliminar_imagen') && $sorteo->imagen) {
            Storage::disk('public')->delete($sorteo->imagen);
            $data['imagen'] = null;
        }

        $sorteo->update($data);

        return redirect()->route('admin.sorteos.show', $sorteo)
            ->with('success', 'Sorteo actualizado exitosamente.');
    }

    public function activar(Sorteo $sorteo): RedirectResponse
    {
        try {
            $this->sorteoService->activar($sorteo);
            return back()->with('success', 'Sorteo activado exitosamente.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cerrar(Sorteo $sorteo): RedirectResponse
    {
        try {
            $this->sorteoService->cerrar($sorteo);
            return back()->with('success', 'Ventas cerradas exitosamente.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function ejecutar(Sorteo $sorteo): RedirectResponse
    {
        try {
            $ganador = $this->sorteoService->ejecutar($sorteo);
            return back()->with('success', "¡Sorteo ejecutado! Número ganador: {$ganador->numero}");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
