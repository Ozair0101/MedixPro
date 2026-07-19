<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `permission` table.
 */
class Permission extends Model
{
    protected $table = 'permission';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'module', 'description', 'is_sensitive',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
    ];
}
