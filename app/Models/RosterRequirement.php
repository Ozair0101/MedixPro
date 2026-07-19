<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `roster_requirement` table.
 */
class RosterRequirement extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'roster_requirement';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'shift_pattern_id', 'staff_category',
        'required_count', 'min_female_count',
    ];

    protected $casts = [
        'required_count' => 'integer',
        'min_female_count' => 'integer',
    ];
}
