<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_condition_map` table.
 */
class MophConditionMap extends Model
{
    use HasUuids;

    protected $table = 'moph_condition_map';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'concept_id', 'priority_condition_id',
    ];
}
