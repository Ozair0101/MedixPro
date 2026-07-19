<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `moph_case_definition` table.
 */
class MophCaseDefinition extends Model
{
    protected $table = 'moph_case_definition';

    protected $primaryKey = 'priority_condition_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'definition_local', 'definition_latin', 'source_reference',
    ];
}
