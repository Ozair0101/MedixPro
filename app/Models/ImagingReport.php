<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `imaging_report` table.
 */
class ImagingReport extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'imaging_report';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'study_id', 'findings', 'impression',
        'status', 'previous_version_id', 'amendment_reason', 'reported_by',
        'reported_at', 'verified_by', 'verified_at', 'rendered_document_id',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
