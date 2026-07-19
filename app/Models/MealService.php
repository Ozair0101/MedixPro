<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `meal_service` table.
 */
class MealService extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'meal_service';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'ward_id', 'service_date', 'meal',
        'diet_type', 'portions_required', 'portions_served',
    ];

    protected $casts = [
        'service_date' => 'date',
        'portions_required' => 'integer',
        'portions_served' => 'integer',
    ];
}
