<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `allergy_manifestation` table.
 */
class AllergyManifestation extends Model
{
    protected $table = 'allergy_manifestation';

    protected $primaryKey = 'reaction_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'concept_id',
    ];
}
