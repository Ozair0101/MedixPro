<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `triage` table.
 */
class Triage extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'triage';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'encounter_id', 'patient_id', 'acuity',
        'presenting_complaint', 'mode_of_arrival', 'is_medico_legal', 'is_mass_casualty',
        'triaged_by', 'triaged_at', 'disposition', 'disposition_at',
    ];

    protected $casts = [
        'acuity' => 'integer',
        'is_medico_legal' => 'boolean',
        'is_mass_casualty' => 'boolean',
        'triaged_at' => 'datetime',
        'disposition_at' => 'datetime',
    ];
}
