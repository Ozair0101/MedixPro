<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `receivable_entry` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class ReceivableEntry extends Model
{
    use BelongsToFacility;

    protected $table = 'receivable_entry';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'account_id', 'patient_id', 'responsible_party_type',
        'responsible_party_id', 'entry_type', 'source_type', 'source_id',
        'against_invoice_id', 'amount', 'currency', 'posting_date',
        'due_date',
    ];

    protected $casts = [
        'amount' => 'float',
        'posting_date' => 'date',
        'due_date' => 'date',
    ];
}
