<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `manufacturer` table.
 */
class Manufacturer extends Model
{
    use HasUuids;

    protected $table = 'manufacturer';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name', 'country',
    ];
}
