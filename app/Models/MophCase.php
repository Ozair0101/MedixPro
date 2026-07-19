<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_case` table.
 */
class MophCase extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'moph_case';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'encounter_id', 'patient_id', 'priority_condition_id',
        'condition_id', 'register_serial', 'register_year', 'service_date',
        'is_new_case', 'age_group', 'sex', 'village',
        'district_pcode', 'province_pcode', 'is_outside_catchment', 'source_register',
        'recorded_at',
    ];

    protected $casts = [
        'register_serial' => 'integer',
        'register_year' => 'integer',
        'service_date' => 'date',
        'is_new_case' => 'boolean',
        'is_outside_catchment' => 'boolean',
        'recorded_at' => 'datetime',
    ];
}
