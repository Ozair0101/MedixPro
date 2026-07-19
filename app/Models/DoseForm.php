<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `dose_form` table.
 */
class DoseForm extends Model
{
    use HasUuids;

    protected $table = 'dose_form';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code', 'name_local', 'name_latin',
    ];
}
