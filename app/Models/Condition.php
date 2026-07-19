<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `condition` table.
 */
class Condition extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'condition';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'concept_id',
        'source_code', 'source_code_system', 'condition_text', 'clinical_status',
        'verification_status', 'category', 'severity', 'onset_date',
        'abatement_date', 'recorded_at', 'recorded_by', 'previous_version_id',
    ];

    protected $casts = [
        'onset_date' => 'date',
        'abatement_date' => 'date',
        'recorded_at' => 'datetime',
    ];
}
