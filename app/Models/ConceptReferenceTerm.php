<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_reference_term` table.
 */
class ConceptReferenceTerm extends Model
{
    use HasUuids;

    protected $table = 'concept_reference_term';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code_system', 'code', 'display', 'version',
        'retired',
    ];

    protected $casts = [
        'retired' => 'boolean',
    ];
}
