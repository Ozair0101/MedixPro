<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `lab_panel` table.
 */
class LabPanel extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'lab_panel';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name_local', 'name_latin',
        'loinc_code', 'billable_item_id',
    ];
}
