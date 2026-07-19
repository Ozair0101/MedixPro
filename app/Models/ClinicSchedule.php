<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `clinic_schedule` table.
 */
class ClinicSchedule extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'clinic_schedule';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'practitioner_id', 'weekday',
        'start_time', 'end_time', 'slot_minutes', 'max_walk_ins',
        'valid_period',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'slot_minutes' => 'integer',
        'max_walk_ins' => 'integer',
    ];
}
