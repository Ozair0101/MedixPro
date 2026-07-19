<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `billable_item` table.
 */
class BillableItem extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'billable_item';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'item_code', 'display_name_local', 'display_name_latin',
        'technical_name', 'category', 'org_unit_id', 'default_unit',
        'gl_revenue_account_id', 'tax_category', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
