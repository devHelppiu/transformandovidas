<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Comercial;
use App\Models\User;
use App\Notifications\BienvenidaNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ComercialController extends Controller
{
    public function index(): View
    {
        $lider = auth()->user()->lider;
        $comerciales = $lider->comerciales()->with('user')->latest()->get();

        return view('lider.comerciales.index', compact('comerciales'));
    }

    public function create(): View
    {
        return view('lider.comerciales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $lider = auth()->user()->lider;

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'comercial',
            'is_active' => true,
        ]);

        // Crear comercial (código se genera automáticamente)
        Comercial::create([
            'user_id' => $user->id,
            'lider_id' => $lider->id,
            'is_active' => true,
        ]);

        // Enviar notificación de bienvenida con credenciales
        $user->notify(new BienvenidaNotification($validated['password'], 'comercial'));

        return redirect()->route('lider.comerciales.index')
            ->with('success', 'Comercial creado exitosamente.');
    }

    public function edit(Comercial $comercial): View
    {
        // Verificar que el comercial pertenece al lider
        $this->authorize('update', $comercial);

        return view('lider.comerciales.edit', compact('comercial'));
    }

    public function update(Request $request, Comercial $comercial): RedirectResponse
    {
        $this->authorize('update', $comercial);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $comercial->user_id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Actualizar usuario
        $comercial->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $comercial->user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('lider.comerciales.index')
            ->with('success', 'Comercial actualizado exitosamente.');
    }

    public function destroy(Comercial $comercial): RedirectResponse
    {
        $this->authorize('delete', $comercial);

        // Soft delete: solo desactivar
        $comercial->update(['is_active' => false]);
        $comercial->user->update(['is_active' => false]);

        return redirect()->route('lider.comerciales.index')
            ->with('success', 'Comercial desactivado exitosamente.');
    }

    public function toggleActive(Comercial $comercial): RedirectResponse
    {
        $this->authorize('update', $comercial);

        $comercial->update(['is_active' => !$comercial->is_active]);
        $comercial->user->update(['is_active' => $comercial->is_active]);

        $status = $comercial->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Comercial {$status} exitosamente.");
    }
}
