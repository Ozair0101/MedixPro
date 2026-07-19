<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_result` table.
 */
class LabResult extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_result';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'analysis_id', 'analyte_concept_id', 'value_numeric',
        'value_concept_id', 'value_text', 'unit_concept_id', 'ref_low',
        'ref_high', 'ref_text', 'interpretation', 'is_critical',
        'result_status', 'previous_version_id', 'correction_reason', 'entered_by',
        'entered_at',
    ];

    protected $casts = [
        'value_numeric' => 'float',
        'ref_low' => 'float',
        'ref_high' => 'float',
        'is_critical' => 'boolean',
        'entered_at' => 'datetime',
    ];
}
