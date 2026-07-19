<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_count` table.
 */
class StockCount extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_count';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'count_number', 'location_id', 'count_type',
        'status', 'counted_at', 'counted_by', 'approved_by',
    ];

    protected $casts = [
        'counted_at' => 'datetime',
    ];
}
