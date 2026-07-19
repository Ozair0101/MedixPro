<?php

namespace App\Observers;

use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Records create / update / delete against any model it is attached to.
 *
 * Writes only. Reads are logged explicitly via AuditLogger::recordRead(),
 * because an observer cannot tell a deliberate chart lookup from an incidental
 * relation hydration, and logging both buries the signal.
 *
 * Attach in AppServiceProvider:
 *     Patient::observe(AuditableObserver::class);
 */
class AuditableObserver
{
    /**
     * Never write these values into the audit trail. A trail that contains the
     * password hashes it was meant to protect is a liability, not a control.
     */
    private const REDACT = [
        'password', 'password_hash', 'remember_token', 'api_token',
        'two_factor_secret', 'content_hash', 'signature_blob',
    ];

    public function created(Model $model): void
    {
        AuditLogger::record(
            action: 'create',
            table: $model->getTable(),
            recordId: (string) $model->getKey(),
            patientId: $this->patientId($model),
            newValues: $this->scrub($model->getAttributes()),
        );
    }

    public function updated(Model $model): void
    {
        $changed = $model->getChanges();

        if ($changed === []) {
            return;
        }

        // Store only what actually changed, plus the prior values of those same
        // fields. Snapshotting whole rows makes the trail enormous and makes
        // "what changed?" a diffing exercise at read time.
        $before = [];
        foreach (array_keys($changed) as $key) {
            $before[$key] = $model->getOriginal($key);
        }

        AuditLogger::record(
            action: 'update',
            table: $model->getTable(),
            recordId: (string) $model->getKey(),
            patientId: $this->patientId($model),
            oldValues: $this->scrub($before),
            newValues: $this->scrub($changed),
        );
    }

    public function deleted(Model $model): void
    {
        AuditLogger::record(
            action: 'delete',
            table: $model->getTable(),
            recordId: (string) $model->getKey(),
            patientId: $this->patientId($model),
            oldValues: $this->scrub($model->getAttributes()),
        );
    }

    /**
     * Denormalize the patient so "who accessed this patient's record" is a
     * single indexed query rather than a union across every clinical table.
     */
    private function patientId(Model $model): ?string
    {
        foreach (['patient_id', 'id'] as $candidate) {
            if ($candidate === 'id' && $model->getTable() !== 'patient') {
                continue;
            }
            $value = $model->getAttribute($candidate);
            if ($value !== null) {
                return (string) $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function scrub(array $values): array
    {
        foreach (self::REDACT as $key) {
            if (array_key_exists($key, $values)) {
                $values[$key] = '[redacted]';
            }
        }

        return $values;
    }
}
