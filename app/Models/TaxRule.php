<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `tax_rule` table.
 */
class TaxRule extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'tax_rule';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'tax_type', 'tax_category', 'rate',
        'is_credit_eligible', 'legal_citation', 'valid_at',
    ];

    protected $casts = [
        'rate' => 'float',
        'is_credit_eligible' => 'boolean',
    ];
}
