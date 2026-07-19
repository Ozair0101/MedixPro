<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `therapeutic_moiety` table.
 */
class TherapeuticMoiety extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'therapeutic_moiety';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'name_latin', 'name_local',
    ];
}
