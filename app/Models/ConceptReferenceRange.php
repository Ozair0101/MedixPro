<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_reference_range` table.
 */
class ConceptReferenceRange extends Model
{
    use HasUuids;

    protected $table = 'concept_reference_range';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'concept_id', 'sex', 'min_age_days', 'max_age_days',
        'low_normal', 'high_normal', 'low_valid', 'high_valid',
        'low_critical', 'high_critical', 'low_reporting', 'high_reporting',
        'valid_period',
    ];

    protected $casts = [
        'min_age_days' => 'integer',
        'max_age_days' => 'integer',
        'low_normal' => 'float',
        'high_normal' => 'float',
        'low_valid' => 'float',
        'high_valid' => 'float',
        'low_critical' => 'float',
        'high_critical' => 'float',
        'low_reporting' => 'float',
        'high_reporting' => 'float',
    ];
}
