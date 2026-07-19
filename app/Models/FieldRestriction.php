<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `field_restriction` table.
 */
class FieldRestriction extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'field_restriction';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'table_name', 'column_name', 'required_permission',
        'applies_to_print',
    ];

    protected $casts = [
        'applies_to_print' => 'boolean',
    ];
}
