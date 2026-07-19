<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_test` table.
 */
class LabTest extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_test';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'concept_id', 'section_id',
        'name_local', 'name_latin', 'loinc_code', 'specimen_type_concept_id',
        'container_type', 'minimum_volume_ml', 'result_datatype', 'unit_concept_id',
        'significant_digits', 'tat_average_minutes', 'tat_warning_minutes', 'tat_max_minutes',
        'is_orderable', 'is_reportable', 'requires_fasting', 'billable_item_id',
        'valid_period',
    ];

    protected $casts = [
        'minimum_volume_ml' => 'float',
        'significant_digits' => 'integer',
        'tat_average_minutes' => 'integer',
        'tat_warning_minutes' => 'integer',
        'tat_max_minutes' => 'integer',
        'is_orderable' => 'boolean',
        'is_reportable' => 'boolean',
        'requires_fasting' => 'boolean',
    ];
}
