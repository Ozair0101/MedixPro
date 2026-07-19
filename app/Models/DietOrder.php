<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `diet_order` table.
 */
class DietOrder extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'diet_order';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'admission_id', 'order_id',
        'diet_type', 'special_instructions', 'valid_period', 'status',
    ];
}
