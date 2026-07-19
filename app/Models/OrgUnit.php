<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrgUnit extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'org_unit';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'parent_id', 'code', 'name_local', 'name_latin',
        'unit_type', 'is_cost_centre', 'is_active',
    ];

    protected $casts = ['is_cost_centre' => 'boolean', 'is_active' => 'boolean'];
}
