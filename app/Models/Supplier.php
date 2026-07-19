<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `supplier` table.
 */
class Supplier extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'supplier';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'code', 'name', 'contact_person',
        'phone', 'email', 'address', 'tin',
        'payment_terms', 'has_business_licence', 'withholding_rate', 'rating',
        'is_active',
    ];

    protected $casts = [
        'has_business_licence' => 'boolean',
        'withholding_rate' => 'float',
        'rating' => 'integer',
        'is_active' => 'boolean',
    ];
}
