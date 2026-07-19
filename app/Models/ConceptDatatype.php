<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_datatype` table.
 */
class ConceptDatatype extends Model
{
    protected $table = 'concept_datatype';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
