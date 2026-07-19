<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `invoice` table.
 */
class Invoice extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'invoice';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'invoice_number', 'account_id', 'patient_id',
        'recipient_party_id', 'status', 'currency', 'subtotal',
        'discount_total', 'tax_total', 'rounding_adjustment', 'total_gross',
        'fx_rate_id', 'issued_at', 'issued_by', 'due_date',
        'cancelled_reason',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_total' => 'float',
        'tax_total' => 'float',
        'rounding_adjustment' => 'float',
        'total_gross' => 'float',
        'issued_at' => 'datetime',
        'due_date' => 'date',
    ];
}
