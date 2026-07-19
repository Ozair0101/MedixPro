<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `leave_request` table.
 */
class LeaveRequest extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'leave_request';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'employee_id', 'leave_type_id', 'leave_period',
        'days_count', 'reason', 'status', 'requested_at',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'days_count' => 'float',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
}
