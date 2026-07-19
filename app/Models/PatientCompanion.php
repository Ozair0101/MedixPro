<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An accompanying relative, optionally the patient's mahram.
 *
 * OPTIONAL AT THE DATA LAYER, DELIBERATELY. Recording a companion is a service
 * to the patient; REQUIRING one would make this software an instrument that
 * turns away widows, orphans and displaced women who arrive alone. Nothing in
 * the schema, the API or the UI enforces its presence (ADR-010).
 */
class PatientCompanion extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'patient_companion';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'name_local', 'relationship', 'is_mahram',
        'phone', 'can_receive_results', 'can_consent_on_behalf',
        'national_id', 'is_active',
    ];

    protected $casts = [
        'is_mahram' => 'boolean',
        'can_receive_results' => 'boolean',
        'can_consent_on_behalf' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
