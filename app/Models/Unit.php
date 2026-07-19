<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `unit` table.
 */
class Unit extends Model
{
    use HasUuids;

    protected $table = 'unit';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'uom_category_id', 'code', 'name_local', 'name_latin',
        'factor_to_reference', 'is_reference', 'rounding_precision', 'rounding_mode',
    ];

    protected $casts = [
        'factor_to_reference' => 'float',
        'is_reference' => 'boolean',
        'rounding_precision' => 'float',
    ];
}
