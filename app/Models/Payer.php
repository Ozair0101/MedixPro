<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `payer` table.
 */
class Payer extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'payer';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'party_id', 'code', 'name',
        'payer_type', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
