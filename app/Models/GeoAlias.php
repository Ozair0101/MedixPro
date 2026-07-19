<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `geo_alias` table.
 */
class GeoAlias extends Model
{
    use HasUuids;

    protected $table = 'geo_alias';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'pcode', 'alias',
    ];
}
