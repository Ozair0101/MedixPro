<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `practitioner_qualification` table.
 */
class PractitionerQualification extends Model
{
    use HasUuids;

    protected $table = 'practitioner_qualification';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'practitioner_id', 'qualification', 'issuing_body', 'licence_number',
        'valid_period',
    ];
}
