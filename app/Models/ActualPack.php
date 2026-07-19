<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `actual_pack` table.
 */
class ActualPack extends Model
{
    use HasUuids;

    protected $table = 'actual_pack';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'amp_id', 'pack_quantity', 'pack_unit',
    ];

    protected $casts = [
        'pack_quantity' => 'float',
    ];
}
