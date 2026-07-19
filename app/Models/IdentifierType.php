<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Reference table of identifier kinds and their validation rules.
 *
 * `validation_regex` is FORMAT ONLY. There is no public check-digit
 * specification for the e-Tazkira, so a guessed checksum would reject valid
 * patients at the registration desk — a worse failure than accepting the
 * occasional typo (ADR-006).
 */
class IdentifierType extends Model
{
    protected $table = 'identifier_type';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $casts = [
        'is_unique' => 'boolean',
        'normalize_digits' => 'boolean',
    ];
}
