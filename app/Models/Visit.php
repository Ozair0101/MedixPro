<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A contiguous contact with the hospital.
 *
 * One visit may contain several encounters — registration, triage,
 * consultation, procedure — which is why the two are separate. Collapsing them
 * means a patient seen twice in one attendance either loses the second contact
 * or appears to have attended twice.
 */
class Visit extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'visit';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'visit_number', 'visit_type',
        'period', 'admission_id', 'referral_source', 'referred_from_facility',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }
}
