<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `virtual_product` table.
 */
class VirtualProduct extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'virtual_product';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'vtm_id', 'name_latin', 'name_local',
        'dose_form_id', 'is_combination', 'prescribable', 'is_controlled',
        'controlled_schedule', 'is_essential_medicine', 'is_high_alert', 'retired',
    ];

    protected $casts = [
        'is_combination' => 'boolean',
        'prescribable' => 'boolean',
        'is_controlled' => 'boolean',
        'is_essential_medicine' => 'boolean',
        'is_high_alert' => 'boolean',
        'retired' => 'boolean',
    ];
}
