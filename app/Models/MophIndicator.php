<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_indicator` table.
 */
class MophIndicator extends Model
{
    use HasUuids;

    protected $table = 'moph_indicator';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code', 'name_local', 'name_latin', 'domain',
        'definition', 'numerator_spec', 'denominator_spec', 'data_source',
        'frequency', 'is_computable_here',
    ];

    protected $casts = [
        'is_computable_here' => 'boolean',
    ];
}
