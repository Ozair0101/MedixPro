<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `transfusion_reaction` table.
 */
class TransfusionReaction extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'transfusion_reaction';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'transfusion_id', 'reaction_type', 'severity',
        'onset_at', 'signs_symptoms', 'action_taken', 'reported_by',
        'investigated', 'investigation_outcome',
    ];

    protected $casts = [
        'onset_at' => 'datetime',
        'investigated' => 'boolean',
    ];
}
