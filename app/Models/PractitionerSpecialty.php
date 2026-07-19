<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `practitioner_specialty` table.
 */
class PractitionerSpecialty extends Model
{
    protected $table = 'practitioner_specialty';

    protected $primaryKey = 'practitioner_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'specialty_code',
    ];
}
