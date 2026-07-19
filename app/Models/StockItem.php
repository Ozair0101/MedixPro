<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `stock_item` table.
 */
class StockItem extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'stock_item';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'item_code', 'name_local', 'name_latin',
        'item_category', 'ampp_id', 'vmp_id', 'base_unit',
        'lot_control', 'expiry_control', 'serialized', 'is_controlled',
        'cold_chain', 'reorder_level', 'max_level', 'removal_lead_days',
        'is_active',
    ];

    protected $casts = [
        'lot_control' => 'boolean',
        'expiry_control' => 'boolean',
        'serialized' => 'boolean',
        'is_controlled' => 'boolean',
        'cold_chain' => 'boolean',
        'reorder_level' => 'float',
        'max_level' => 'float',
        'removal_lead_days' => 'integer',
        'is_active' => 'boolean',
    ];
}
