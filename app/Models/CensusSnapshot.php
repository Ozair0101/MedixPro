<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `census_snapshot` table.
 */
class CensusSnapshot extends Model
{
    use BelongsToFacility;

    protected $table = 'census_snapshot';

    protected $primaryKey = 'facility_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'census_date', 'ward_id', 'occupied_beds', 'available_beds',
        'admissions', 'discharges', 'deaths', 'transfers_in',
        'transfers_out', 'computed_at',
    ];

    protected $casts = [
        'census_date' => 'date',
        'occupied_beds' => 'integer',
        'available_beds' => 'integer',
        'admissions' => 'integer',
        'discharges' => 'integer',
        'deaths' => 'integer',
        'transfers_in' => 'integer',
        'transfers_out' => 'integer',
        'computed_at' => 'datetime',
    ];
}
