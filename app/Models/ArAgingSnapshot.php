<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `ar_aging_snapshot` table.
 */
class ArAgingSnapshot extends Model
{
    use BelongsToFacility;

    protected $table = 'ar_aging_snapshot';

    protected $primaryKey = 'as_of_date';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'account_id', 'responsible_party_type', 'responsible_party_id',
        'bucket_id', 'outstanding',
    ];

    protected $casts = [
        'bucket_id' => 'integer',
        'outstanding' => 'float',
    ];
}
