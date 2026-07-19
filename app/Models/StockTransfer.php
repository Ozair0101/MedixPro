<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_transfer` table.
 */
class StockTransfer extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_transfer';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'transfer_number', 'requisition_id', 'source_location_id',
        'transit_location_id', 'dest_location_id', 'status', 'dispatched_at',
        'dispatched_by', 'received_at', 'received_by',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];
}
