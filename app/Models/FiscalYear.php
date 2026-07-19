<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `fiscal_year` table.
 */
class FiscalYear extends Model
{
    use HasUuids;

    protected $table = 'fiscal_year';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'shamsi_year', 'label', 'period', 'is_stub',
    ];

    protected $casts = [
        'shamsi_year' => 'integer',
        'is_stub' => 'boolean',
    ];
}
