<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `allergy_reaction` table.
 */
class AllergyReaction extends Model
{
    use HasUuids;

    protected $table = 'allergy_reaction';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'allergy_id', 'severity', 'exposure_route_concept_id', 'occurred_at',
        'description',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];
}
