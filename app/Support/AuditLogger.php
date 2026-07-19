<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Writes the audit trail (ADR-010).
 *
 * WHY READS ARE LOGGED, NOT JUST WRITES
 * -------------------------------------
 * A SELECT fires no database trigger, so the append-only triggers on audit_log
 * protect the trail but cannot populate it for reads. Read auditing has to
 * happen here, in the application.
 *
 * And in this operating environment reads are the events that matter. The fact
 * that someone LOOKED UP a female patient's reproductive-health record is the
 * security incident; nothing was modified, so a write-only trail shows nothing
 * at all. Which permission guards the data determines whether its access is
 * logged — see permission.is_sensitive.
 *
 * Never let auditing break care. Every write here is wrapped: if the audit
 * insert fails, it is reported to the error log and the clinical operation
 * proceeds. A hospital that cannot register a patient because the audit table
 * is full is worse than one with an incomplete trail.
 */
class AuditLogger
{
    /** @var array<string, bool>|null lazily loaded permission => is_sensitive */
    private static ?array $sensitiveCache = null;

    public static function record(
        string $action,
        ?string $table = null,
        ?string $recordId = null,
        ?string $patientId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $overrideReason = null,
    ): void {
        try {
            $user = Auth::user();
            $facilityId = $user?->facility_id
                ?? DB::selectOne("SELECT nullif(current_setting('app.facility_id', true), '') AS f")?->f;

            if ($facilityId === null) {
                // Nothing to attribute the event to. Logging it without a
                // facility would place it outside every RLS policy, so it would
                // be invisible to the people meant to review it.
                return;
            }

            $request = request();

            DB::table('audit_log')->insert([
                'facility_id' => $facilityId,
                'occurred_at' => now(),
                'user_id' => $user?->id,
                // Denormalized so the trail survives the user row being removed.
                'username' => $user?->username ?? 'system',
                'action' => $action,
                'table_name' => $table,
                'record_id' => $recordId,
                'patient_id' => $patientId,
                'old_values' => $oldValues !== null ? json_encode($oldValues) : null,
                'new_values' => $newValues !== null ? json_encode($newValues) : null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'override_reason' => $overrideReason,
            ]);
        } catch (Throwable $e) {
            // Audit failure must never block clinical work.
            report($e);
        }
    }

    /**
     * Log a read of patient-identifiable data.
     *
     * Call this from controllers that return patient data. It is deliberately
     * explicit rather than automatic: a model observer cannot distinguish
     * "loaded this patient to display their chart" from "loaded this patient as
     * a foreign-key hydration inside an unrelated query", and logging the
     * latter would bury the former in noise.
     */
    public static function recordRead(
        string $table,
        string $recordId,
        ?string $patientId = null,
    ): void {
        self::record('read', $table, $recordId, $patientId ?? $recordId);
    }

    /**
     * Log a consciously overridden soft rule — a male provider treating a
     * female patient in an emergency, dispensing past an interaction alert,
     * and so on. The reason is mandatory: an override with no recorded
     * justification is indistinguishable from a mistake.
     */
    public static function recordOverride(
        string $table,
        string $recordId,
        string $reason,
        ?string $patientId = null,
    ): void {
        self::record('override', $table, $recordId, $patientId,
            overrideReason: $reason);
    }

    public static function recordExport(string $exportType, string $exportId): void
    {
        // Exports leave the building; they are always audited.
        self::record('export', 'data_export', $exportId);
    }

    /**
     * Is access guarded by this permission sensitive enough to log reads?
     */
    public static function isSensitive(string $permissionCode): bool
    {
        if (self::$sensitiveCache === null) {
            try {
                self::$sensitiveCache = DB::table('permission')
                    ->pluck('is_sensitive', 'code')
                    ->map(fn ($v) => (bool) $v)
                    ->all();
            } catch (Throwable) {
                // Fail SAFE: if the permission table cannot be read, treat
                // everything as sensitive rather than silently logging nothing.
                return true;
            }
        }

        return self::$sensitiveCache[$permissionCode] ?? true;
    }

    /** Test hook. */
    public static function flushCache(): void
    {
        self::$sensitiveCache = null;
    }
}
