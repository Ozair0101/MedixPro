<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `surgery_implant` table.
 */
class SurgeryImplant extends Model
{
    use HasUuids;

    protected $table = 'surgery_implant';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'surgery_id', 'stock_item_id', 'stock_lot_id', 'serial_number',
        'description', 'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
}
