<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFacility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * An application user, backed by `app_user`.
 *
 * This previously mapped to Laravel's default `users` table, which the HMIS
 * schema does not create — so authentication queried a table that did not
 * exist, while `hmis:provision-facility` created administrators in `app_user`
 * that nothing could log in as.
 *
 * Three differences from a stock Laravel user, each deliberate:
 *
 *  - Login is by USERNAME, not email. Most staff have no work email address,
 *    and requiring one would make accounts undistributable.
 *  - `gender` is NOT NULL. It drives provider assignment, ward segregation and
 *    mahram rules, so it is identity data rather than a profile preference
 *    (ADR-010).
 *  - `facility_id` is what the RLS session variable is set from — taken from
 *    the authenticated user, never from a request header (ADR-002).
 */
class User extends Authenticatable
{
    use BelongsToFacility, HasApiTokens, HasUuids, Notifiable;

    protected $table = 'app_user';

    public $timestamps = false;

    protected $fillable = [
        'facility_id', 'username', 'email', 'password_hash', 'display_name',
        'gender', 'staff_id', 'preferred_locale', 'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    /**
     * The column is `password_hash`, not `password` — named for what it holds.
     * Laravel's guard asks through this method, so the difference stays
     * invisible to the framework.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * Every permission this user holds, via their roles.
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return $this->roles()
            ->join('role_permission', 'role_permission.role_id', '=', 'role.id')
            ->pluck('role_permission.permission_code')
            ->unique()
            ->values()
            ->all();
    }

    public function hasPermission(string $code): bool
    {
        return in_array($code, $this->permissions(), true);
    }

    /**
     * Is the account locked out after repeated failures?
     *
     * The schema models `failed_attempts` and `locked_until`. Without this the
     * columns are decoration and the login endpoint is open to brute force.
     */
    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function canAuthenticate(): bool
    {
        return $this->is_active && ! $this->isLocked();
    }
}
