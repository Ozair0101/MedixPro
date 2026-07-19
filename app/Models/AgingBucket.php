<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `aging_bucket` table.
 */
class AgingBucket extends Model
{
    protected $table = 'aging_bucket';

    public $timestamps = false;

    protected $fillable = [
        'label', 'days_gte', 'days_lt',
    ];

    protected $casts = [
        'days_gte' => 'integer',
        'days_lt' => 'integer',
    ];
}
