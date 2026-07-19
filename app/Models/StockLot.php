<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_lot` table.
 */
class StockLot extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_lot';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'stock_item_id', 'lot_number', 'serial_number',
        'expiry_date', 'removal_date', 'manufacture_date', 'gtin',
        'supplier_id', 'lot_status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'removal_date' => 'date',
        'manufacture_date' => 'date',
    ];
}
