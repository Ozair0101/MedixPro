<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `number_series` table.
 */
class NumberSeries extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'number_series';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'series_code', 'prefix', 'suffix',
        'padding', 'next_value', 'resets_annually', 'current_period',
    ];

    protected $casts = [
        'padding' => 'integer',
        'next_value' => 'integer',
        'resets_annually' => 'boolean',
    ];
}
