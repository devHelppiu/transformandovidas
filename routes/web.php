<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Cliente;
use App\Http\Controllers\Comercial;
use App\Http\Controllers\ConsultaTicketController;
use App\Http\Controllers\HelppiuWebhookController;
use App\Http\Controllers\PagoResultadoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SorteoPublicoController;
use Illuminate\Support\Facades\Route;

// --- Landing ---
Route::get('/', function () {
    $sorteos = \App\Models\Sorteo::where('estado', 'activo')->latest()->get();
    return view('welcome', compact('sorteos'));
});

// --- Referral link (redirects to active sorteo) ---
Route::get('/ref/{codigo}', function (string $codigo) {
    $sorteo = \App\Models\Sorteo::where('estado', 'activo')->latest()->first();
    
    if (!$sorteo) {
        return redirect('/')->with('info', 'No hay sorteos activos en este momento.');
    }
    
    return redirect()->route('sorteo.publico', ['sorteo' => $sorteo, 'ref' => $codigo]);
})->name('referido');

// --- Public sorteo page (captures referral) ---
Route::get('/sorteo/{sorteo}', [SorteoPublicoController::class, 'show'])
    ->middleware('referral')
    ->name('sorteo.publico');

// --- Public ticket purchase (rate limited to prevent abuse) ---
Route::post('/sorteo/{sorteo}/comprar', [Cliente\TicketController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('tickets.comprar');
Route::get('/ticket/{ticket}', [ConsultaTicketController::class, 'show'])->name('ticket.detalle');
Route::post('/ticket/{ticket}/verificar', [ConsultaTicketController::class, 'verificar'])
    ->middleware('throttle:5,1')
    ->name('ticket.verificar');
Route::post('/ticket/{ticket}/comprobante', [Cliente\PagoController::class, 'subirComprobante'])
    ->middleware('throttle:5,1')
    ->name('ticket.comprobante');

// --- Helppiu Pay: payment result pages ---
Route::get('/pago/exitoso', [PagoResultadoController::class, 'exitoso'])->name('pago.exitoso');
Route::get('/pago/cancelado', [PagoResultadoController::class, 'cancelado'])->name('pago.cancelado');

// --- Helppiu Pay: webhook (CSRF exempt, rate limited) ---
Route::post('/webhooks/helppiu', [HelppiuWebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('webhooks.helppiu');

// --- Ticket lookup by email (rate limited to prevent enumeration) ---
Route::get('/mis-tickets', [ConsultaTicketController::class, 'index'])->name('consulta.tickets');
Route::post('/mis-tickets', [ConsultaTicketController::class, 'buscar'])
    ->middleware('throttle:10,1')
    ->name('consulta.tickets.buscar');

// --- Generic dashboard redirect (Breeze default) ---
Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->dashboardRoute());
})->middleware(['auth'])->name('dashboard');

// --- Profile (Breeze) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Sorteos
    Route::resource('sorteos', Admin\SorteoController::class)->except(['destroy']);
    Route::post('/sorteos/{sorteo}/activar', [Admin\SorteoController::class, 'activar'])->name('sorteos.activar');
    Route::post('/sorteos/{sorteo}/cerrar', [Admin\SorteoController::class, 'cerrar'])->name('sorteos.cerrar');
    Route::post('/sorteos/{sorteo}/ejecutar', [Admin\SorteoController::class, 'ejecutar'])->name('sorteos.ejecutar');

    // Combos (inline on sorteo page)
    Route::post('/sorteos/{sorteo}/combos', [Admin\ComboController::class, 'store'])->name('combos.store');
    Route::post('/combos/{combo}/toggle', [Admin\ComboController::class, 'toggleActive'])->name('combos.toggle');
    Route::delete('/combos/{combo}', [Admin\ComboController::class, 'destroy'])->name('combos.destroy');

    // Comerciales
    Route::resource('comerciales', Admin\ComercialController::class)->except(['destroy', 'show'])->parameters(['comerciales' => 'comercial']);
    Route::post('/comerciales/{comercial}/toggle-active', [Admin\ComercialController::class, 'toggleActive'])->name('comerciales.toggle-active');

    // Pagos
    Route::get('/pagos', [Admin\PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/{pago}', [Admin\PagoController::class, 'show'])->name('pagos.show');
    Route::post('/pagos/{pago}/verificar', [Admin\PagoController::class, 'verificar'])->name('pagos.verificar');

    // Comprobante (private file viewer)
    Route::get('/ticket/{ticket}/comprobante', [Cliente\PagoController::class, 'verComprobante'])->name('ticket.comprobante.ver');

    // Reportes
    Route::get('/reportes', [Admin\ReporteController::class, 'index'])->name('reportes.index');
});

// ============================================================
// COMERCIAL ROUTES
// ============================================================
Route::middleware(['auth', 'role:comercial'])->prefix('comercial')->name('comercial.')->group(function () {
    Route::get('/dashboard', [Comercial\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/comisiones', [Comercial\ComisionController::class, 'index'])->name('comisiones.index');
});

// ============================================================
// CLIENTE ROUTES
// ============================================================
Route::middleware(['auth', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', [Cliente\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/historial', [Cliente\TicketController::class, 'historial'])->name('historial');
});

require __DIR__.'/auth.php';
