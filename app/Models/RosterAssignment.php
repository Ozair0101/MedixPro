<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `roster_assignment` table.
 */
class RosterAssignment extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'roster_assignment';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'employee_id', 'org_unit_id', 'shift_pattern_id',
        'shift', 'assignment_type', 'status',
    ];
}
