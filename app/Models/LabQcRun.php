<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_qc_run` table.
 */
class LabQcRun extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_qc_run';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'test_id', 'control_lot', 'control_level',
        'expected_value', 'expected_sd', 'observed_value', 'is_in_control',
        'westgard_rule_violated', 'run_at', 'run_by', 'action_taken',
    ];

    protected $casts = [
        'expected_value' => 'float',
        'expected_sd' => 'float',
        'observed_value' => 'float',
        'is_in_control' => 'boolean',
        'run_at' => 'datetime',
    ];
}
