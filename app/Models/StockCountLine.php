<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_count_line` table.
 */
class StockCountLine extends Model
{
    use HasUuids;

    protected $table = 'stock_count_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'stock_count_id', 'stock_item_id', 'stock_lot_id', 'system_qty',
        'counted_qty', 'variance_reason', 'ledger_id',
    ];

    protected $casts = [
        'system_qty' => 'float',
        'counted_qty' => 'float',
        'ledger_id' => 'integer',
    ];
}
