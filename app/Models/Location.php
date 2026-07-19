<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `location` table.
 */
class Location extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'location';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'parent_id', 'org_unit_id', 'name',
        'physical_type', 'status', 'path',
    ];
}
