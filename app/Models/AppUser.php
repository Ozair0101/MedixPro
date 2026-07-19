<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Generated from the `app_user` table.
 */
class AppUser extends Model
{
    use BelongsToFacility, HasUuids;

    protected $table = 'app_user';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'username', 'email', 'password_hash',
        'display_name', 'gender', 'staff_id', 'preferred_locale',
        'is_active', 'last_login_at', 'failed_attempts', 'locked_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'failed_attempts' => 'integer',
        'locked_until' => 'datetime',
    ];
}
