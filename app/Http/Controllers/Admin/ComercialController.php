<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComercialRequest;
use App\Models\Comercial;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ComercialController extends Controller
{
    public function index()
    {
        $comerciales = Comercial::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.comerciales.index', compact('comerciales'));
    }

    public function create()
    {
        return view('admin.comerciales.create');
    }

    public function store(StoreComercialRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'comercial',
        ]);

        Comercial::create([
            'user_id' => $user->id,
            'comision_tipo' => $request->comision_tipo,
            'comision_valor' => $request->comision_valor,
        ]);

        return redirect()->route('admin.comerciales.index')
            ->with('success', 'Comercial creado exitosamente.');
    }

    public function edit(Comercial $comercial)
    {
        $comercial->load('user');
        return view('admin.comerciales.edit', compact('comercial'));
    }

    public function update(Request $request, Comercial $comercial): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $comercial->user_id],
            'phone' => ['nullable', 'string', 'max:20'],
            'comision_tipo' => ['nullable', 'in:porcentaje,fijo,meta'],
            'comision_valor' => ['nullable', 'numeric', 'min:0'],
        ]);

        $comercial->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $comercial->update([
            'comision_tipo' => $request->comision_tipo,
            'comision_valor' => $request->comision_valor,
        ]);

        return redirect()->route('admin.comerciales.index')
            ->with('success', 'Comercial actualizado exitosamente.');
    }

    public function toggleActive(Comercial $comercial): RedirectResponse
    {
        $comercial->update(['is_active' => ! $comercial->is_active]);
        $comercial->user->update(['is_active' => ! $comercial->user->is_active]);

        $status = $comercial->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Comercial {$status} exitosamente.");
    }
}
