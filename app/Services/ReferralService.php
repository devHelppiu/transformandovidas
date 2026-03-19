<?php

namespace App\Services;

use App\Models\Comercial;

class ReferralService
{
    /**
     * Resolve comercial_id from manual code input, session, or null.
     * Manual input takes priority over session.
     */
    public function resolve(?string $codigoManual, ?int $sessionComercialId): ?int
    {
        // Priority 1: manual code input
        if ($codigoManual) {
            $comercial = Comercial::where('codigo_ref', strtoupper(trim($codigoManual)))
                ->where('is_active', true)
                ->first();

            if ($comercial) {
                return $comercial->id;
            }
        }

        // Priority 2: session referral
        if ($sessionComercialId) {
            // Verify it still exists and is active
            $exists = Comercial::where('id', $sessionComercialId)
                ->where('is_active', true)
                ->exists();

            return $exists ? $sessionComercialId : null;
        }

        return null;
    }
}
