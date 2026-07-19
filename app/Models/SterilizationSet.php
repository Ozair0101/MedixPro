<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `sterilization_set` table.
 */
class SterilizationSet extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'sterilization_set';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'set_code', 'name', 'contents',
    ];

    protected $casts = [
        'contents' => 'array',
    ];
}
