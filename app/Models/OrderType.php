<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `order_type` table.
 */
class OrderType extends Model
{
    protected $table = 'order_type';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name', 'requires_specimen',
    ];

    protected $casts = [
        'requires_specimen' => 'boolean',
    ];
}
