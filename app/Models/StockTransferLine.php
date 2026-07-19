<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_transfer_line` table.
 */
class StockTransferLine extends Model
{
    use HasUuids;

    protected $table = 'stock_transfer_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'transfer_id', 'stock_item_id', 'stock_lot_id', 'qty_dispatched',
        'qty_received', 'unit_id',
    ];

    protected $casts = [
        'qty_dispatched' => 'float',
        'qty_received' => 'float',
    ];
}
