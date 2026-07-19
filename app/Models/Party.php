<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `party` table.
 */
class Party extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'party';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'party_type', 'person_id', 'organization_name',
        'tin', 'phone', 'address',
    ];
}
