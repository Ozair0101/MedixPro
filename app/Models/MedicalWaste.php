<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `medical_waste` table.
 */
class MedicalWaste extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'medical_waste';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'org_unit_id', 'waste_category', 'weight_kg',
        'collected_at', 'disposal_method', 'disposed_at', 'handled_by',
    ];

    protected $casts = [
        'weight_kg' => 'float',
        'collected_at' => 'datetime',
        'disposed_at' => 'datetime',
    ];
}
