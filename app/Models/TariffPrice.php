<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `tariff_price` table.
 */
class TariffPrice extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'tariff_price';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'tariff_id', 'item_id', 'unit_id',
        'min_quantity', 'price_type', 'amount', 'percent',
        'valid_at', 'created_by',
    ];

    protected $casts = [
        'min_quantity' => 'float',
        'amount' => 'float',
        'percent' => 'float',
    ];
}
