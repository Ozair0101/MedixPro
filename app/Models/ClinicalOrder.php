<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * A clinical order — the hinge between the clinical record and everything
 * downstream. Every lab test, every dispense and every charge traces back to
 * one of these.
 *
 * ORDERS ARE IMMUTABLE AND VERSIONED. Changing a live order in place destroys
 * the record of what was actually instructed at the time: if a dose was halved
 * this morning, the morning administration must still be judged against the
 * original. So a revision creates a NEW row pointing back via
 * previous_order_id, and the old one is stopped.
 *
 * Class-table inheritance: DrugOrder and DiagnosticOrder share this row's id
 * as both their primary key and their foreign key, which keeps them type-safe
 * and joinable — unlike a runtime-dispatched table-name registry.
 */
class ClinicalOrder extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'clinical_order';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'order_number', 'requisition_number', 'patient_id',
        'encounter_id', 'order_type_code', 'concept_id', 'status', 'intent',
        'priority', 'order_action', 'previous_order_id', 'ordered_by',
        'ordered_at', 'is_verbal_order', 'countersigned_by', 'countersigned_at',
        'scheduled_at', 'instructions', 'order_reason', 'stopped_at',
        'fulfiller_status',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'countersigned_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'stopped_at' => 'datetime',
        'is_verbal_order' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'ordered_by');
    }

    public function drugOrder(): HasOne
    {
        return $this->hasOne(DrugOrder::class, 'id');
    }

    public function previousOrder(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_order_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * A verbal order is valid but provisional: it must be countersigned by the
     * prescriber before it counts as a signed instruction.
     */
    public function awaitsCountersignature(): bool
    {
        return $this->is_verbal_order && $this->countersigned_by === null;
    }
}
