<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_reflex_rule` table.
 */
class LabReflexRule extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_reflex_rule';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'trigger_test_id', 'condition', 'reflex_test_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
