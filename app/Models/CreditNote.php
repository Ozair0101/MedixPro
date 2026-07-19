<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `credit_note` table.
 */
class CreditNote extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'credit_note';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'credit_note_number', 'invoice_id', 'reason_code',
        'total_amount', 'issued_at', 'issued_by', 'approved_by',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'issued_at' => 'datetime',
    ];
}
