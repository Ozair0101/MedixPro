<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `housekeeping_task` table.
 */
class HousekeepingTask extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'housekeeping_task';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'location_id', 'task_type', 'triggered_by_discharge_id',
        'requested_at', 'started_at', 'completed_at', 'assigned_to',
        'status',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
