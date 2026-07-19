<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_reference_map` table.
 */
class ConceptReferenceMap extends Model
{
    use HasUuids;

    protected $table = 'concept_reference_map';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'concept_id', 'term_id', 'map_type',
    ];
}
