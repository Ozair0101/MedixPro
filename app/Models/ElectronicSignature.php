<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `electronic_signature` table.
 *
 * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add
 * mutation methods here. Corrections are new rows, not edits.
 */
class ElectronicSignature extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'electronic_signature';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'target_table', 'target_id', 'target_version',
        'signer_id', 'purpose', 'signed_at', 'auth_method',
        'content_hash', 'signed_manifestation', 'ip_address',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];
}
