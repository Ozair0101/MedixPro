<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `notification` table.
 */
class Notification extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'notification';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'channel', 'recipient_user_id', 'recipient_phone',
        'template_code', 'body', 'consent_verified', 'status',
        'queued_at', 'sent_at', 'error',
    ];

    protected $casts = [
        'consent_verified' => 'boolean',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
