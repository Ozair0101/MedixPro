<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_allocation` table.
 */
class StockAllocation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_allocation';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'stock_item_id', 'location_id', 'stock_lot_id',
        'qty', 'reference_type', 'reference_id', 'status',
        'expires_at', 'idempotency_key',
    ];

    protected $casts = [
        'qty' => 'float',
        'expires_at' => 'datetime',
    ];
}
