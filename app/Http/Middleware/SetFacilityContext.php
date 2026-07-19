<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the PostgreSQL session variable that drives Row-Level Security (ADR-002).
 *
 * Every tenant-scoped table has a policy of the form:
 *
 *     USING (facility_id = current_facility_id())
 *
 * so this one line is what stands between a request and another facility's
 * patient records. Two rules follow, and neither is negotiable:
 *
 *  1. The facility comes from the AUTHENTICATED USER, never from a request
 *     header. The old `X-Hospital-Id` header defaulted to 1 and was re-read
 *     by hand in every handler — any client could read any facility's data by
 *     changing one header value. That mechanism is gone; do not reintroduce it.
 *
 *  2. set_config(..., is_local: true) makes the setting TRANSACTION-LOCAL.
 *     Under PgBouncer in transaction pooling mode a plain `SET` persists on a
 *     server connection that is then handed to the next client — a
 *     cross-tenant data leak. Transaction-local scoping is what makes this
 *     pooler-safe, which is why the request runs inside a transaction below.
 */
class SetFacilityContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $facilityId = $request->user()?->facility_id;

        if ($facilityId === null) {
            // No authenticated facility: leave the setting empty so RLS denies
            // everything rather than falling back to a default. Failing closed
            // is the point.
            return $next($request);
        }

        // Wrap the request in a transaction so the transaction-local setting
        // has a scope that spans the whole request.
        return DB::transaction(function () use ($request, $next, $facilityId) {
            DB::statement(
                'SELECT set_config(?, ?, true)',
                ['app.facility_id', (string) $facilityId]
            );

            return $next($request);
        });
    }
}
