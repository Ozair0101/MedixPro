<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `encounter_status_history` table.
 */
class EncounterStatusHistory extends Model
{
    use HasUuids;

    protected $table = 'encounter_status_history';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'encounter_id', 'status', 'period', 'changed_by',
    ];
}
