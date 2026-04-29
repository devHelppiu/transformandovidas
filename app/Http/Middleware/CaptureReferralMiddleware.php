<?php

namespace App\Http\Middleware;

use App\Models\Comercial;
use App\Models\Lider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferralMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref');

        if ($ref && preg_match('/^TV-[A-Z0-9]{5,6}$/i', $ref)) {
            $ref = strtoupper($ref);

            // Primero buscar en comerciales
            $comercial = Comercial::where('codigo_ref', $ref)
                ->where('is_active', true)
                ->first();

            if ($comercial) {
                $request->session()->put('referral_comercial_id', $comercial->id);
                $request->session()->put('referral_codigo', $comercial->codigo_ref);
                $request->session()->forget('referral_lider_id');
            } else {
                // Si no es comercial, buscar en lideres
                $lider = Lider::where('codigo_ref', $ref)
                    ->where('is_active', true)
                    ->first();

                if ($lider) {
                    $request->session()->put('referral_lider_id', $lider->id);
                    $request->session()->put('referral_codigo', $lider->codigo_ref);
                    $request->session()->forget('referral_comercial_id');
                }
            }
        }

        return $next($request);
    }
}
