<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `role_permission` table.
 */
class RolePermission extends Model
{
    protected $table = 'role_permission';

    protected $primaryKey = 'role_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'permission_code',
    ];
}
