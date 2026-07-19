<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `code_system` table.
 */
class CodeSystem extends Model
{
    protected $table = 'code_system';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name', 'uri', 'version',
    ];
}
