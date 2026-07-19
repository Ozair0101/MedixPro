<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `linen_transaction` table.
 */
class LinenTransaction extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'linen_transaction';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'stock_item_id', 'transaction_type',
        'quantity', 'occurred_at', 'recorded_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'occurred_at' => 'datetime',
    ];
}
