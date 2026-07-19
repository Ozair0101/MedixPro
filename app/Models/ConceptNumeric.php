<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_numeric` table.
 */
class ConceptNumeric extends Model
{
    protected $table = 'concept_numeric';

    protected $primaryKey = 'concept_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'unit_concept_id', 'allow_decimal', 'display_precision',
    ];

    protected $casts = [
        'allow_decimal' => 'boolean',
        'display_precision' => 'integer',
    ];
}
