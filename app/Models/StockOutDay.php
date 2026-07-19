<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_out_day` table.
 */
class StockOutDay extends Model
{
    use BelongsToFacility;

    protected $table = 'stock_out_day';

    protected $primaryKey = 'facility_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'stock_item_id', 'observed_date', 'was_out_of_stock', 'qty_on_hand',
    ];

    protected $casts = [
        'observed_date' => 'date',
        'was_out_of_stock' => 'boolean',
        'qty_on_hand' => 'float',
    ];
}
