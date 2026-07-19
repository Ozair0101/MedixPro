<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `app_setting` table.
 */
class AppSetting extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'app_setting';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'key', 'value', 'data_type',
        'description', 'updated_by',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
