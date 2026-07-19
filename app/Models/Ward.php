<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `ward` table.
 */
class Ward extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'ward';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'sex_policy', 'ward_type', 'is_paediatric',
    ];

    protected $casts = [
        'is_paediatric' => 'boolean',
    ];
}
