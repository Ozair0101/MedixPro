<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A clinician.
 *
 * `gender` is NOT NULL and clinically load-bearing: it drives provider
 * assignment, ward segregation and mahram rules. With roughly 18% of specialist
 * physicians and 29% of nurses female, female-provider availability is the
 * binding constraint on care for female patients, so it must be queryable
 * rather than inferred (ADR-010).
 */
class Practitioner extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'practitioner';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'person_id', 'user_id', 'moph_staff_code',
        'name_local', 'name_latin', 'gender', 'practitioner_type',
        'primary_org_unit_id', 'phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'primary_org_unit_id');
    }

    public function canTreatWithoutOverride(string $patientGender): bool
    {
        return $patientGender !== 'F' || $this->gender === 'F';
    }
}
