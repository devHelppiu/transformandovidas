<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SorteoPublicoController extends Controller
{
    public function show(Sorteo $sorteo)
    {
        $ticketsVendidos = $sorteo->ticketsVendidos();
        $combos = $sorteo->combos()->where('activo', true)->orderBy('cantidad')->get();

        return view('sorteo.show', compact('sorteo', 'ticketsVendidos', 'combos'));
    }

    /**
     * Verifica si un número está disponible en el sorteo.
     * GET /sorteo/{sorteo}/verificar-numero?numero=1234
     */
    public function verificarNumero(Request $request, Sorteo $sorteo): JsonResponse
    {
        $numero = $request->query('numero');

        // Validar formato del número (4 dígitos)
        if (!$numero || !preg_match('/^\d{1,4}$/', $numero)) {
            return response()->json([
                'disponible' => false,
                'error' => 'Número inválido. Debe ser de 1 a 4 dígitos.',
            ], 400);
        }

        // Normalizar a 4 dígitos
        $numeroFormateado = str_pad($numero, 4, '0', STR_PAD_LEFT);

        // Verificar si el número está dentro del rango del sorteo
        $numeroInt = (int) $numero;
        if ($numeroInt < 0 || $numeroInt >= $sorteo->total_tickets) {
            return response()->json([
                'disponible' => false,
                'numero' => $numeroFormateado,
                'mensaje' => "El número debe estar entre 0000 y " . str_pad($sorteo->total_tickets - 1, 4, '0', STR_PAD_LEFT),
            ]);
        }

        // Verificar si existe un ticket con ese número
        $ticketExistente = Ticket::where('sorteo_id', $sorteo->id)
            ->where('numero', $numeroFormateado)
            ->whereIn('estado', ['reservado', 'pagado'])
            ->first();

        if ($ticketExistente) {
            return response()->json([
                'disponible' => false,
                'numero' => $numeroFormateado,
                'estado' => $ticketExistente->estado === 'pagado' ? 'vendido' : 'reservado',
                'mensaje' => $ticketExistente->estado === 'pagado' 
                    ? 'Este número ya fue vendido' 
                    : 'Este número está reservado temporalmente',
            ]);
        }

        return response()->json([
            'disponible' => true,
            'numero' => $numeroFormateado,
            'mensaje' => '¡Disponible!',
        ]);
    }

    /**
     * Devuelve el estado de números en un rango para el number picker.
     * GET /sorteo/{sorteo}/numeros?from=0&to=99
     */
    public function numeros(Request $request, Sorteo $sorteo): JsonResponse
    {
        $from = (int) $request->query('from', 0);
        $to = (int) $request->query('to', 99);

        // Limitar el rango a 100 números máximo
        if ($to - $from > 100) {
            $to = $from + 99;
        }

        // Asegurar que el rango es válido
        $from = max(0, min($from, $sorteo->total_tickets - 1));
        $to = max($from, min($to, $sorteo->total_tickets - 1));

        $numeros = $sorteo->numerosEstadosEnRango($from, $to);
        $disponiblesGlobal = count($sorteo->numerosDisponibles());

        return response()->json([
            'from' => $from,
            'to' => $to,
            'total_disponibles_global' => $disponiblesGlobal,
            'total_tickets' => $sorteo->total_tickets,
            'numeros' => $numeros,
        ]);
    }
}
