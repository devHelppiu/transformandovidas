<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coordinador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoordinadorController extends Controller
{
    public function index()
    {
        $coordinadores = Coordinador::with('user')
            ->withCount('lideres')
            ->latest()
            ->paginate(15);
        return view('admin.coordinadores.index', compact('coordinadores'));
    }

    public function create()
    {
        return view('admin.coordinadores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'coordinador',
        ]);

        Coordinador::create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.coordinadores.index')
            ->with('success', 'Coordinador creado exitosamente.');
    }

    public function show(Coordinador $coordinador)
    {
        $coordinador->load(['user', 'lideres.user', 'lideres.comerciales']);
        
        $stats = [
            'lideres_count' => $coordinador->lideres()->count(),
            'lideres_activos' => $coordinador->lideresActivos()->count(),
            'comerciales_count' => $coordinador->totalComercialesActivos(),
            'comision_acumulada' => $coordinador->comisiones()->sum('monto_comision'),
            'comision_pendiente' => $coordinador->comisiones()->where('estado', 'pendiente')->sum('monto_comision'),
        ];

        return view('admin.coordinadores.show', compact('coordinador', 'stats'));
    }

    public function edit(Coordinador $coordinador)
    {
        return view('admin.coordinadores.edit', compact('coordinador'));
    }

    public function update(Request $request, Coordinador $coordinador)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $coordinador->user_id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $coordinador->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $coordinador->user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.coordinadores.index')
            ->with('success', 'Coordinador actualizado exitosamente.');
    }

    public function toggleActive(Coordinador $coordinador)
    {
        $coordinador->update(['is_active' => !$coordinador->is_active]);
        $status = $coordinador->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Coordinador {$status}.");
    }
}
