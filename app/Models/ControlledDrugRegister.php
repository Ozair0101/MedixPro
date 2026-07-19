<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `controlled_drug_register` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class ControlledDrugRegister extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'controlled_drug_register';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'stock_item_id', 'location_id', 'entry_type',
        'quantity', 'balance_after', 'patient_id', 'performed_by',
        'witnessed_by', 'occurred_at', 'notes',
    ];

    protected $casts = [
        'quantity' => 'float',
        'balance_after' => 'float',
        'occurred_at' => 'datetime',
    ];
}
