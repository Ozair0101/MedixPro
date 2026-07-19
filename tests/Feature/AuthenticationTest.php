<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Authentication against `app_user`.
 *
 * The first test here guards a failure that took real debugging: login is a
 * chicken-and-egg with Row-Level Security. The facility context is derived from
 * the authenticated user, but RLS needs that context before it will return the
 * user row to authenticate against — so with strict isolation the login query
 * matches zero rows and NO password is ever correct.
 *
 * The `auth_lookup` policy resolves it by permitting SELECT on app_user only
 * while no facility context is set. If someone removes that policy, the first
 * test fails loudly instead of every login silently returning "incorrect
 * credentials".
 */
class AuthenticationTest extends TestCase
{
    private string $facilityId;

    protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Authentication targets the PostgreSQL schema.');
        }

        DB::beginTransaction();

        $this->facilityId = (string) Str::uuid();
        DB::table('facility')->insert([
            'id' => $this->facilityId, 'name_local' => 'شفاخانه تست',
            'name_latin' => 'Test Hospital', 'facility_type' => 'district_hospital',
        ]);

        DB::statement('SELECT set_config(?, ?, true)', ['app.facility_id', $this->facilityId]);

        DB::table('app_user')->insert([
            'id' => (string) Str::uuid(),
            'facility_id' => $this->facilityId,
            'username' => 'testadmin',
            'password_hash' => Hash::make('correct-horse'),
            'display_name' => 'Test Administrator',
            'gender' => 'F',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * THE regression test. Without the auth_lookup policy this returns 0 and
     * every login in the system fails with "incorrect credentials".
     */
    public function test_a_user_is_readable_before_any_facility_context_is_set(): void
    {
        DB::statement("SELECT set_config('app.facility_id', '', true)");

        $found = User::where('username', 'testadmin')->count();

        $this->assertSame(1, $found,
            'Login could not read the user row. RLS needs a facility context that '
            .'only the user can supply, so the auth_lookup policy must permit '
            .'SELECT while no context is set.');
    }

    public function test_writes_still_require_a_facility_context(): void
    {
        DB::statement("SELECT set_config('app.facility_id', '', true)");

        // The carve-out is SELECT-only: nothing may be created before
        // authentication.
        $this->expectException(QueryException::class);

        DB::table('app_user')->insert([
            'id' => (string) Str::uuid(),
            'facility_id' => $this->facilityId,
            'username' => 'smuggled',
            'password_hash' => Hash::make('x'),
            'display_name' => 'Smuggled',
            'gender' => 'M',
        ]);
    }

    public function test_login_succeeds_with_a_username(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'correct-horse',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['access_token', 'user' => ['username', 'facility', 'permissions']]);

        $this->assertSame('testadmin', $response->json('user.username'));
    }

    public function test_the_password_hash_is_never_returned(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'correct-horse',
        ]);

        $this->assertStringNotContainsString('password', strtolower($response->getContent()),
            'A login response must not leak the password hash.');
    }

    public function test_a_wrong_password_is_rejected(): void
    {
        $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_an_unknown_user_gets_the_same_error_as_a_wrong_password(): void
    {
        $unknown = $this->postJson('/api/auth/login',
            ['username' => 'nobody', 'password' => 'x'])->json('errors.username.0');

        $wrongPassword = $this->postJson('/api/auth/login',
            ['username' => 'testadmin', 'password' => 'x'])->json('errors.username.0');

        // Identical wording, so the endpoint is not a user-enumeration oracle.
        $this->assertSame($unknown, $wrongPassword);
    }

    public function test_repeated_failures_lock_the_account(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login',
                ['username' => 'testadmin', 'password' => 'wrong']);
        }

        // The correct password must now be refused: the schema models
        // failed_attempts and locked_until, and without enforcement they are
        // decoration and the endpoint is open to brute force.
        $response = $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'correct-horse',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('locked', strtolower($response->getContent()));
    }

    public function test_a_deactivated_account_cannot_log_in(): void
    {
        DB::table('app_user')->where('username', 'testadmin')->update(['is_active' => false]);

        $this->postJson('/api/auth/login', [
            'username' => 'testadmin',
            'password' => 'correct-horse',
        ])->assertStatus(422);
    }

    public function test_persian_digits_in_a_username_are_normalized(): void
    {
        DB::table('app_user')->where('username', 'testadmin')->update(['username' => 'nurse01']);

        // What an Afghan keyboard produces. Without normalization this is a
        // different string and the login silently fails (ADR-007).
        $this->postJson('/api/auth/login', [
            'username' => 'nurse۰۱',
            'password' => 'correct-horse',
        ])->assertOk();
    }
}
