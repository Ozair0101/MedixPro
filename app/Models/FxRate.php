<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `fx_rate` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class FxRate extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'fx_rate';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'base_currency', 'quote_currency', 'rate_date',
        'cash_buy', 'cash_sell', 'transfer_buy', 'transfer_sell',
        'source', 'raw_snapshot', 'recorded_at', 'recorded_by',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'cash_buy' => 'float',
        'cash_sell' => 'float',
        'transfer_buy' => 'float',
        'transfer_sell' => 'float',
        'recorded_at' => 'datetime',
    ];
}
