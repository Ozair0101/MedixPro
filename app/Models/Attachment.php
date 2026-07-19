<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `attachment` table.
 */
class Attachment extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'attachment';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'owner_type', 'owner_id', 'file_name',
        'mime_type', 'byte_size', 'storage_path', 'sha256',
        'is_sensitive', 'uploaded_by', 'uploaded_at',
    ];

    protected $casts = [
        'byte_size' => 'integer',
        'is_sensitive' => 'boolean',
        'uploaded_at' => 'datetime',
    ];
}
