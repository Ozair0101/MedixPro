<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `allergy` table.
 */
class Allergy extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'allergy';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'substance_concept_id', 'substance_text',
        'allergy_type', 'category', 'criticality', 'clinical_status',
        'verification_status', 'onset_date', 'last_occurrence', 'recorded_at',
        'recorded_by',
    ];

    protected $casts = [
        'onset_date' => 'date',
        'last_occurrence' => 'date',
        'recorded_at' => 'datetime',
    ];
}
