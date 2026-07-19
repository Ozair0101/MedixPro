<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A dispensing event.
 *
 * Replaces the pre-migration model, which decremented a quantity column on a
 * batch row directly. Stock movements now go through the append-only ledger,
 * so every issue is auditable and reversible.
 */
class Dispense extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'dispense';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'dispense_number', 'patient_id', 'encounter_id',
        'drug_order_id', 'location_id', 'dispense_type', 'status',
        'screened_interactions', 'screened_allergy', 'screening_overridden',
        'override_reason', 'dispensed_by', 'witnessed_by', 'dispensed_at',
        'counselled',
    ];

    protected $casts = [
        'screened_interactions' => 'boolean',
        'screened_allergy' => 'boolean',
        'screening_overridden' => 'boolean',
        'counselled' => 'boolean',
        'dispensed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function drugOrder(): BelongsTo
    {
        return $this->belongsTo(DrugOrder::class, 'drug_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DispenseItem::class);
    }
}
