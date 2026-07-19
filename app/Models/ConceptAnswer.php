<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_answer` table.
 */
class ConceptAnswer extends Model
{
    protected $table = 'concept_answer';

    protected $primaryKey = 'concept_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'answer_concept_id', 'sort_weight',
    ];

    protected $casts = [
        'sort_weight' => 'integer',
    ];
}
