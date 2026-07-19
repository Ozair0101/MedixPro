<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_requisition_line` table.
 */
class StockRequisitionLine extends Model
{
    use HasUuids;

    protected $table = 'stock_requisition_line';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'requisition_id', 'stock_item_id', 'qty_requested', 'qty_approved',
        'qty_fulfilled', 'unit_id',
    ];

    protected $casts = [
        'qty_requested' => 'float',
        'qty_approved' => 'float',
        'qty_fulfilled' => 'float',
    ];
}
