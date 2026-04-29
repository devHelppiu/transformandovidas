<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Lider;
use App\Models\User;
use App\Notifications\BienvenidaNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LiderController extends Controller
{
    public function index(): View
    {
        $coordinador = auth()->user()->coordinador;
        $lideres = $coordinador->lideres()->with('user')->latest()->get();

        return view('coordinador.lideres.index', compact('lideres'));
    }

    public function create(): View
    {
        return view('coordinador.lideres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $coordinador = auth()->user()->coordinador;

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'lider',
            'is_active' => true,
        ]);

        // Crear lider
        Lider::create([
            'user_id' => $user->id,
            'coordinador_id' => $coordinador->id,
            'codigo_ref' => Lider::generarCodigoRef(),
            'is_active' => true,
        ]);

        // Enviar notificación de bienvenida con credenciales
        $user->notify(new BienvenidaNotification($validated['password'], 'lider'));

        return redirect()->route('coordinador.lideres.index')
            ->with('success', 'Líder creado exitosamente.');
    }

    public function edit(Lider $lider): View
    {
        // Verificar que el lider pertenece al coordinador
        $this->authorize('update', $lider);

        return view('coordinador.lideres.edit', compact('lider'));
    }

    public function update(Request $request, Lider $lider): RedirectResponse
    {
        $this->authorize('update', $lider);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $lider->user_id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Actualizar usuario
        $lider->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $lider->user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('coordinador.lideres.index')
            ->with('success', 'Líder actualizado exitosamente.');
    }

    public function destroy(Lider $lider): RedirectResponse
    {
        $this->authorize('delete', $lider);

        // Soft delete: solo desactivar
        $lider->update(['is_active' => false]);
        $lider->user->update(['is_active' => false]);

        return redirect()->route('coordinador.lideres.index')
            ->with('success', 'Líder desactivado exitosamente.');
    }

    public function toggleActive(Lider $lider): RedirectResponse
    {
        $this->authorize('update', $lider);

        $lider->update(['is_active' => !$lider->is_active]);
        $lider->user->update(['is_active' => $lider->is_active]);

        $status = $lider->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Líder {$status} exitosamente.");
    }
}
