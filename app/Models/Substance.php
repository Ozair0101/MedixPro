<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `substance` table.
 */
class Substance extends Model
{
    use HasUuids;

    protected $table = 'substance';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name_latin', 'name_local', 'concept_id',
    ];
}
