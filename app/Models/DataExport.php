<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `data_export` table.
 */
class DataExport extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'data_export';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'export_type', 'format', 'requested_by',
        'requested_at', 'completed_at', 'row_count', 'byte_size',
        'storage_path', 'sha256', 'status',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
        'row_count' => 'integer',
        'byte_size' => 'integer',
    ];
}
