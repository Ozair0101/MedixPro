<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `employee_credential` table.
 */
class EmployeeCredential extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'employee_credential';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'employee_id', 'credential_type', 'credential_number',
        'issuing_body', 'valid_period', 'document_id',
    ];
}
