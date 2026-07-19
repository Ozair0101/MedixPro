<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `payment_allocation` table.
 */
class PaymentAllocation extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'payment_allocation';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'payment_id', 'target_type', 'target_id',
        'amount', 'allocated_at', 'delinked_at', 'delinked_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'allocated_at' => 'datetime',
        'delinked_at' => 'datetime',
    ];
}
