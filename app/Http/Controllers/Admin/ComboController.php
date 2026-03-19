<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Sorteo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function store(Request $request, Sorteo $sorteo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:2', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
        ]);

        $sorteo->combos()->create($validated);

        return back()->with('success', 'Combo creado exitosamente.');
    }

    public function toggleActive(Combo $combo): RedirectResponse
    {
        $combo->update(['activo' => !$combo->activo]);

        return back()->with('success', $combo->activo ? 'Combo activado.' : 'Combo desactivado.');
    }

    public function destroy(Combo $combo): RedirectResponse
    {
        $combo->delete();

        return back()->with('success', 'Combo eliminado.');
    }
}
