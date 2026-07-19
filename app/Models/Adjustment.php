<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `adjustment` table.
 */
class Adjustment extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'adjustment';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'account_id', 'invoice_line_id', 'kind',
        'reason_code', 'amount', 'posted_at', 'posted_by',
        'approved_by', 'reverses_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'posted_at' => 'datetime',
    ];
}
