<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `tariff` table.
 */
class Tariff extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'tariff';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'payer_id',
        'currency', 'priority', 'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];
}
