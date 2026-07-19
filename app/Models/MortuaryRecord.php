<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `mortuary_record` table.
 */
class MortuaryRecord extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'mortuary_record';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'patient_id', 'deceased_name', 'received_at',
        'storage_unit', 'death_time', 'cause_of_death', 'is_medico_legal',
        'released_at', 'released_to_name', 'released_to_relation', 'released_to_id_number',
        'authorized_by', 'death_certificate_no',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'death_time' => 'datetime',
        'is_medico_legal' => 'boolean',
        'released_at' => 'datetime',
    ];
}
