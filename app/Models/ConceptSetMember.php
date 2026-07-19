<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `concept_set_member` table.
 */
class ConceptSetMember extends Model
{
    protected $table = 'concept_set_member';

    protected $primaryKey = 'set_concept_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'member_concept_id', 'sort_weight',
    ];

    protected $casts = [
        'sort_weight' => 'integer',
    ];
}
