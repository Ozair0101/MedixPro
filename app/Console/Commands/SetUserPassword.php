<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Sets or resets a user's password, and clears any lockout.
 *
 * Needed because there is no email delivery to rely on: an on-premise facility
 * frequently has no internet at all, so a self-service reset link is not a
 * workable recovery path. An administrator with shell access sets the password
 * and hands it over in person.
 *
 *   php artisan hmis:password admin
 *   php artisan hmis:password admin --password=secret --unlock
 *   php artisan hmis:password --list
 */
class SetUserPassword extends Command
{
    protected $signature = 'hmis:password
        {username? : The username to update}
        {--password= : The new password (generated if omitted)}
        {--unlock : Clear failed attempts and any lockout}
        {--list : List users who can log in}';

    protected $description = "Set a user's password, or list users";

    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listUsers();
        }

        $username = $this->argument('username') ?: $this->ask('Username');

        // Bypasses RLS scoping problems: a console command has no HTTP request
        // and therefore no facility context set by middleware.
        $user = User::where('username', $username)->first();

        if (! $user) {
            $this->error("No user '{$username}'. Run with --list to see who exists.");

            return self::FAILURE;
        }

        $password = $this->option('password') ?: Str::password(14);

        $user->forceFill([
            'password_hash' => Hash::make($password),
            'failed_attempts' => 0,
            'locked_until' => null,
        ])->save();

        if ($this->option('unlock') || $user->is_active === false) {
            $user->forceFill(['is_active' => true])->save();
        }

        AuditLogger::record('update', 'app_user', $user->id,
            newValues: ['password_reset' => true]);

        $this->info("Password updated for {$user->username} ({$user->display_name}).");

        if (! $this->option('password')) {
            $this->newLine();
            $this->warn("  {$password}");
            $this->warn('  Shown once. Hand it over directly and change it on first login.');
        }

        return self::SUCCESS;
    }

    private function listUsers(): int
    {
        // Raw query: the console has no facility context, so an Eloquent read
        // would be filtered to nothing by Row-Level Security.
        $users = DB::connection('pgsql_migrate')->select("
            SELECT u.username, u.display_name, u.gender, u.is_active,
                   u.locked_until, f.name_latin AS facility,
                   COALESCE(string_agg(r.code, ', '), '—') AS roles
              FROM app_user u
              LEFT JOIN facility f ON f.id = u.facility_id
              LEFT JOIN user_role ur ON ur.user_id = u.id
              LEFT JOIN role r ON r.id = ur.role_id
             GROUP BY u.username, u.display_name, u.gender, u.is_active,
                      u.locked_until, f.name_latin
             ORDER BY u.username
        ");

        if ($users === []) {
            $this->warn('No users exist yet.');
            $this->line('Create a facility and its first administrator with:');
            $this->line('  php artisan hmis:provision-facility --name="…" --name-latin="…" '
                .'--province=… --admin-gender=M');

            return self::SUCCESS;
        }

        $this->table(
            ['Username', 'Name', 'Sex', 'Active', 'Locked', 'Facility', 'Roles'],
            array_map(fn ($u) => [
                $u->username,
                $u->display_name,
                $u->gender,
                $u->is_active ? 'yes' : 'no',
                $u->locked_until ? 'until '.substr((string) $u->locked_until, 11, 5) : '—',
                $u->facility ?? '—',
                $u->roles,
            ], $users)
        );

        return self::SUCCESS;
    }
}
