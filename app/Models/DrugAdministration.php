<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `drug_administration` table.
 */
class DrugAdministration extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'drug_administration';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'mar_slot_id', 'drug_order_id', 'patient_id',
        'dispense_id', 'stock_lot_id', 'dose_given', 'dose_unit_concept_id',
        'route_concept_id', 'site', 'administered_at', 'administered_by',
        'witnessed_by', 'status', 'not_done_reason', 'notes',
    ];

    protected $casts = [
        'dose_given' => 'float',
        'administered_at' => 'datetime',
    ];
}
