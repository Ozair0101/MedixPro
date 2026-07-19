<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `charge_item` table.
 */
class ChargeItem extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'charge_item';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'encounter_id', 'account_id',
        'source_type', 'source_id', 'item_id', 'quantity',
        'unit_id', 'tariff_id', 'tariff_price_id', 'unit_price',
        'gross_amount', 'override_amount', 'override_reason', 'status',
        'service_date', 'performer_id', 'cost_centre_id', 'parent_id',
        'entered_by', 'entered_at',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'gross_amount' => 'float',
        'override_amount' => 'float',
        'service_date' => 'datetime',
        'entered_at' => 'datetime',
    ];
}
