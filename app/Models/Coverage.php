<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `coverage` table.
 */
class Coverage extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'coverage';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'payer_id', 'policy_number',
        'kind', 'valid_period', 'copay_percent', 'priority',
    ];

    protected $casts = [
        'copay_percent' => 'float',
        'priority' => 'integer',
    ];
}
