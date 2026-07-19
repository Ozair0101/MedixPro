<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `role` table.
 */
class Role extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'role';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
