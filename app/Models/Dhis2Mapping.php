<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `dhis2_mapping` table.
 */
class Dhis2Mapping extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'dhis2_mapping';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'source_type', 'source_key', 'data_element_uid',
        'category_option_combo_uid', 'org_unit_uid',
    ];
}
