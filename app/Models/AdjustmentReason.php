<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `adjustment_reason` table.
 */
class AdjustmentReason extends Model
{
    protected $table = 'adjustment_reason';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name_local', 'name_latin', 'kind', 'requires_approval',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
    ];
}
