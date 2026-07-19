<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_name` table.
 */
class ConceptName extends Model
{
    use HasUuids;

    protected $table = 'concept_name';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'concept_id', 'name', 'locale', 'is_preferred',
        'name_type',
    ];

    protected $casts = [
        'is_preferred' => 'boolean',
    ];
}
