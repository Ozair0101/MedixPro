<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The hinge of the whole system.
 *
 * Clinical documentation, orders, charges and statistics all attach here:
 *
 *     patient → visit → ENCOUNTER → order → result → charge → invoice
 *
 * Status and class vocabularies are FHIR's, used verbatim, so the interop
 * projection stays a rename rather than a translation.
 */
class Encounter extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'encounter';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'visit_id', 'patient_id', 'encounter_type', 'class_code',
        'status', 'period', 'org_unit_id', 'primary_practitioner_id',
        'gender_override_reason', 'chief_complaint', 'parent_encounter_id',
        'is_first_ever_visit', 'created_by',
    ];

    protected $casts = [
        'is_first_ever_visit' => 'boolean',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'primary_practitioner_id');
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vitals::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ClinicalOrder::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['planned', 'arrived', 'triaged', 'in-progress'], true);
    }
}
