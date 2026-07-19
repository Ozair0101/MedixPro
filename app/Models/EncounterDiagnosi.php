<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `encounter_diagnosis` table.
 */
class EncounterDiagnosi extends Model
{
    protected $table = 'encounter_diagnosis';

    protected $primaryKey = 'encounter_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'condition_id', 'diagnosis_use', 'rank', 'is_new_case',
    ];

    protected $casts = [
        'rank' => 'integer',
        'is_new_case' => 'boolean',
    ];
}
