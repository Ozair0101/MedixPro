<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_analysis` table.
 */
class LabAnalysi extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_analysis';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'sample_item_id', 'test_id', 'panel_id',
        'order_id', 'section_id', 'status', 'revision',
        'parent_analysis_id', 'reflex_triggered', 'analyzer_id', 'started_at',
        'completed_at', 'technical_accepted_by', 'technical_accepted_at', 'clinically_validated_by',
        'clinically_validated_at', 'released_at', 'is_reportable', 'referred_to_org',
        'referred_at',
    ];

    protected $casts = [
        'revision' => 'integer',
        'reflex_triggered' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'technical_accepted_at' => 'datetime',
        'clinically_validated_at' => 'datetime',
        'released_at' => 'datetime',
        'is_reportable' => 'boolean',
        'referred_at' => 'datetime',
    ];
}
