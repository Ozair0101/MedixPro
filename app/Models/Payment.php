<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `payment` table.
 */
class Payment extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'payment';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'receipt_number', 'payer_party_id', 'payer_kind',
        'direction', 'method', 'amount', 'currency',
        'external_ref', 'received_at', 'cashier_shift_id', 'received_by',
        'status', 'reverses_id', 'approved_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'received_at' => 'datetime',
    ];
}
