<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `virtual_product_ingredient` table.
 */
class VirtualProductIngredient extends Model
{
    protected $table = 'virtual_product_ingredient';

    protected $primaryKey = 'vmp_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'substance_id', 'strength_num_value', 'strength_num_unit', 'strength_den_value',
        'strength_den_unit',
    ];

    protected $casts = [
        'strength_num_value' => 'float',
        'strength_den_value' => 'float',
    ];
}
