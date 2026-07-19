<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `observation` table.
 */
class Observation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'observation';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'order_id',
        'concept_id', 'observed_at', 'parent_observation_id', 'value_numeric',
        'value_concept_id', 'value_text', 'value_datetime', 'value_boolean',
        'value_complex', 'absent_reason', 'unit_concept_id', 'ref_low',
        'ref_high', 'interpretation', 'status', 'previous_version_id',
        'comments', 'form_path', 'recorded_by', 'recorded_at',
        'voided', 'voided_by', 'void_reason',
    ];

    protected $casts = [
        'observed_at' => 'datetime',
        'value_numeric' => 'float',
        'value_datetime' => 'datetime',
        'value_boolean' => 'boolean',
        'ref_low' => 'float',
        'ref_high' => 'float',
        'recorded_at' => 'datetime',
        'voided' => 'boolean',
    ];
}
