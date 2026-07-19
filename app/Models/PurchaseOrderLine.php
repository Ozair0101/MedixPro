<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `purchase_order_line` table.
 */
class PurchaseOrderLine extends Model
{
    use HasUuids;

    protected $table = 'purchase_order_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'purchase_order_id', 'stock_item_id', 'qty_ordered', 'qty_received',
        'unit_id', 'unit_cost', 'discount', 'tax',
        'line_total',
    ];

    protected $casts = [
        'qty_ordered' => 'float',
        'qty_received' => 'float',
        'unit_cost' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'line_total' => 'float',
    ];
}
