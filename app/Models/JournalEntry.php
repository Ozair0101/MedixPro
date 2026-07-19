<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `journal_entry` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class JournalEntry extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'journal_entry';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'entry_number', 'period_id', 'posting_date',
        'source_type', 'source_id', 'posting_rule_id', 'description',
        'status', 'reverses_id', 'created_by',
    ];

    protected $casts = [
        'entry_number' => 'integer',
        'posting_date' => 'date',
    ];
}
