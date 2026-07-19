<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `virtual_product_atc` table.
 */
class VirtualProductAtc extends Model
{
    protected $table = 'virtual_product_atc';

    protected $primaryKey = 'vmp_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'atc_code', 'ddd_value', 'ddd_unit',
    ];

    protected $casts = [
        'ddd_value' => 'float',
    ];
}
