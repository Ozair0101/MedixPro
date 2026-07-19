<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_balance` table.
 */
class StockBalance extends Model
{
    use BelongsToFacility;

    protected $table = 'stock_balance';

    protected $primaryKey = 'stock_item_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'location_id', 'stock_lot_id', 'qty_on_hand',
        'qty_allocated', 'moving_avg_cost', 'version',
    ];

    protected $casts = [
        'qty_on_hand' => 'float',
        'qty_allocated' => 'float',
        'moving_avg_cost' => 'float',
        'version' => 'integer',
    ];
}
