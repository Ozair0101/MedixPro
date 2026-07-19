<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `leave_type` table.
 */
class LeaveType extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'leave_type';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'annual_entitlement_days',
        'is_paid',
    ];

    protected $casts = [
        'annual_entitlement_days' => 'integer',
        'is_paid' => 'boolean',
    ];
}
