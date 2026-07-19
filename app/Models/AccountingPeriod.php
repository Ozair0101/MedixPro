<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `accounting_period` table.
 */
class AccountingPeriod extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'accounting_period';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'fiscal_year_id', 'period', 'status',
        'closed_by', 'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];
}
