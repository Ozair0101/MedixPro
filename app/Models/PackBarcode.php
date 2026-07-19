<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `pack_barcode` table.
 */
class PackBarcode extends Model
{
    use HasUuids;

    protected $table = 'pack_barcode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ampp_id', 'gtin', 'packaging_level', 'units_per_level',
        'is_primary', 'valid_from', 'valid_to',
    ];

    protected $casts = [
        'units_per_level' => 'integer',
        'is_primary' => 'boolean',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];
}
