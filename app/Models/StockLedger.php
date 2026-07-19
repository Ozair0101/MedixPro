<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_ledger` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class StockLedger extends Model
{
    use BelongsToFacility;

    protected $table = 'stock_ledger';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'stock_item_id', 'location_id', 'stock_lot_id',
        'qty_delta', 'entered_qty', 'entered_unit', 'base_unit',
        'unit_cost', 'txn_type', 'posting_date', 'recorded_at',
        'voucher_type', 'voucher_id', 'idempotency_key', 'performed_by',
    ];

    protected $casts = [
        'qty_delta' => 'float',
        'entered_qty' => 'float',
        'unit_cost' => 'float',
        'posting_date' => 'date',
        'recorded_at' => 'datetime',
    ];
}
