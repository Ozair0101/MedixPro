<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `journal_line` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class JournalLine extends Model
{
    use BelongsToFacility;

    protected $table = 'journal_line';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'entry_id', 'line_no', 'account_id',
        'debit', 'credit', 'currency', 'cost_centre_id',
        'party_id', 'patient_id',
    ];

    protected $casts = [
        'line_no' => 'integer',
        'debit' => 'float',
        'credit' => 'float',
    ];
}
