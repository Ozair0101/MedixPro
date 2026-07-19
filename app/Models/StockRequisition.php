<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_requisition` table.
 */
class StockRequisition extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_requisition';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'requisition_number', 'requesting_location_id', 'fulfilling_location_id',
        'status', 'requested_by', 'requested_at', 'approved_by',
        'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
}
