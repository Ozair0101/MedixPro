<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sterilization_cycle` table.
 */
class SterilizationCycle extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'sterilization_cycle';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'cycle_number', 'sterilizer_asset_id', 'method',
        'temperature_c', 'pressure_bar', 'duration_minutes', 'started_at',
        'completed_at', 'chemical_indicator_pass', 'biological_indicator_pass', 'released',
        'released_by', 'recalled', 'recall_reason',
    ];

    protected $casts = [
        'temperature_c' => 'float',
        'pressure_bar' => 'float',
        'duration_minutes' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'chemical_indicator_pass' => 'boolean',
        'biological_indicator_pass' => 'boolean',
        'released' => 'boolean',
        'recalled' => 'boolean',
    ];
}
