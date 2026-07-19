<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `geo_village_suggestion` table.
 */
class GeoVillageSuggestion extends Model
{
    use HasUuids;

    protected $table = 'geo_village_suggestion';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'district_pcode', 'name', 'use_count',
    ];

    protected $casts = [
        'use_count' => 'integer',
    ];
}
