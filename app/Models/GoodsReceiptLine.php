<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `goods_receipt_line` table.
 */
class GoodsReceiptLine extends Model
{
    use HasUuids;

    protected $table = 'goods_receipt_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'goods_receipt_id', 'purchase_order_line_id', 'stock_item_id', 'stock_lot_id',
        'batch_no', 'expiry_date', 'qty_received', 'qty_accepted',
        'qty_rejected', 'rejection_reason', 'unit_id', 'unit_cost',
        'ledger_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty_received' => 'float',
        'qty_accepted' => 'float',
        'qty_rejected' => 'float',
        'unit_cost' => 'float',
        'ledger_id' => 'integer',
    ];
}
