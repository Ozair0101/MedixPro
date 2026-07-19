<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The drug-specific half of a clinical order.
 *
 * The primary key IS clinical_order.id — class-table inheritance, so a drug
 * order cannot exist without its parent order and the two can never disagree
 * about who the patient is.
 *
 * Prescribing happens at VMP level (the generic — "amoxicillin 500mg capsule"),
 * while dispensing picks a specific pack. That separation is what allows
 * generic substitution without rewriting the prescription.
 */
class DrugOrder extends Model
{
    use BelongsToFacility;

    protected $table = 'drug_order';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id', 'vmp_id', 'drug_non_coded', 'dose', 'dose_unit_concept_id',
        'route_concept_id', 'frequency_code', 'duration_days',
        'quantity_prescribed', 'quantity_unit_concept_id',
        'as_needed', 'as_needed_condition', 'is_high_alert',
        'max_dose_per_period', 'max_dose_period_hours', 'dispense_as_written',
    ];

    protected $casts = [
        'dose' => 'float',
        'quantity_prescribed' => 'float',
        'as_needed' => 'boolean',
        'is_high_alert' => 'boolean',
        'dispense_as_written' => 'boolean',
        'max_dose_per_period' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ClinicalOrder::class, 'id');
    }

    public function dispenses(): HasMany
    {
        return $this->hasMany(Dispense::class, 'drug_order_id');
    }

    /**
     * How much is still owed against this prescription.
     *
     * Partial fills are normal — stock runs out, a patient takes a week's
     * supply now — so "dispensed" is a running total, not a boolean.
     */
    public function quantityRemaining(): float
    {
        $dispensed = (float) $this->dispenses()
            ->whereIn('status', ['completed', 'in_progress'])
            ->join('dispense_item', 'dispense_item.dispense_id', '=', 'dispense.id')
            ->sum('dispense_item.base_quantity');

        return max(0.0, (float) $this->quantity_prescribed - $dispensed);
    }
}
