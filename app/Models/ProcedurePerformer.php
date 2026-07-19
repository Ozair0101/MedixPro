<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `procedure_performer` table.
 */
class ProcedurePerformer extends Model
{
    protected $table = 'procedure_performer';

    protected $primaryKey = 'procedure_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'practitioner_id', 'role',
    ];
}
