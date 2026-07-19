<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `item_uom_conversion` table.
 */
class ItemUomConversion extends Model
{
    protected $table = 'item_uom_conversion';

    protected $primaryKey = 'stock_item_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'unit_id', 'qty_in_base', 'is_purchase_default', 'is_dispense_default',
    ];

    protected $casts = [
        'qty_in_base' => 'float',
        'is_purchase_default' => 'boolean',
        'is_dispense_default' => 'boolean',
    ];
}
