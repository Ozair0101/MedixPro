<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `facility` table.
 */
class Facility extends Model
{
    use HasUuids;

    protected $table = 'facility';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'moph_facility_code', 'name_local', 'name_latin', 'facility_type',
        'moph_form_type', 'province_pcode', 'district_pcode', 'address_detail',
        'phone', 'licensed_beds', 'legal_name_local', 'tin',
        'business_licence_no', 'tax_exempt', 'tax_exemption_ref', 'tax_exemption_from',
        'tax_exemption_to', 'electricity_source', 'electricity_hours_per_day', 'is_active',
    ];

    protected $casts = [
        'licensed_beds' => 'integer',
        'tax_exempt' => 'boolean',
        'tax_exemption_from' => 'date',
        'tax_exemption_to' => 'date',
        'electricity_hours_per_day' => 'float',
        'is_active' => 'boolean',
    ];
}
