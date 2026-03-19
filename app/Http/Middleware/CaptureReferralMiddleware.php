<?php

namespace App\Http\Middleware;

use App\Models\Comercial;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferralMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ref = $request->query('ref');

        if ($ref && preg_match('/^TV-[A-Z0-9]{6}$/i', $ref)) {
            $comercial = Comercial::where('codigo_ref', strtoupper($ref))
                ->where('is_active', true)
                ->first();

            if ($comercial) {
                $request->session()->put('referral_comercial_id', $comercial->id);
                $request->session()->put('referral_codigo', $comercial->codigo_ref);
            }
        }

        return $next($request);
    }
}
