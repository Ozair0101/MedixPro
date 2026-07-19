<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `posting_rule` table.
 */
class PostingRule extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'posting_rule';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'event_type', 'condition', 'legs',
        'version', 'valid_at',
    ];

    protected $casts = [
        'condition' => 'array',
        'legs' => 'array',
        'version' => 'integer',
    ];
}
