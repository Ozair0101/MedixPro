<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `user_role` table.
 */
class UserRole extends Model
{
    protected $table = 'user_role';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'role_id', 'org_unit_id', 'granted_at', 'granted_by',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];
}
