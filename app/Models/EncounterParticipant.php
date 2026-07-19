<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `encounter_participant` table.
 */
class EncounterParticipant extends Model
{
    protected $table = 'encounter_participant';

    protected $primaryKey = 'encounter_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'practitioner_id', 'role', 'period',
    ];
}
