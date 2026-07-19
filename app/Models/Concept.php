<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept` table.
 */
class Concept extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'concept';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'class_code', 'datatype_code', 'short_name',
        'description', 'is_set', 'retired', 'retire_reason',
        'version',
    ];

    protected $casts = [
        'is_set' => 'boolean',
        'retired' => 'boolean',
        'version' => 'integer',
    ];
}
