<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `actual_product` table.
 */
class ActualProduct extends Model
{
    use HasUuids;

    protected $table = 'actual_product';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'vmp_id', 'brand_name', 'manufacturer_id',
    ];
}
