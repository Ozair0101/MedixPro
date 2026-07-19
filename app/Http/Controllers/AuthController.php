<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AuditLogger;
use App\Support\TextNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Failed attempts before the account is locked. */
    private const MAX_ATTEMPTS = 5;

    /** How long a locked account stays locked. */
    private const LOCKOUT_MINUTES = 15;

    /**
     * Log in with a USERNAME.
     *
     * Email is not the credential: most hospital staff have no work email
     * address, and requiring one would make accounts undistributable. An email
     * is still accepted as an alternative for those who have one.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required_without:email', 'string'],
            'email' => ['required_without:username', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Usernames get typed on Afghan keyboards too; fold Persian digits and
        // strip bidi controls before matching, or a valid login silently fails
        // (ADR-007).
        $identifier = TextNormalizer::normalizeInput(
            $request->input('username') ?? $request->input('email')
        );

        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        // Same message and same work whether the user exists or not, so the
        // endpoint does not disclose which usernames are real.
        if (! $user) {
            $this->failGeneric();
        }

        if ($user->isLocked()) {
            throw ValidationException::withMessages([
                'username' => [
                    'This account is locked until '
                    .$user->locked_until->format('H:i').' after repeated failed attempts.',
                ],
            ]);
        }

        if (! $user->is_active) {
            AuditLogger::record('login_failed', 'app_user', $user->id);

            throw ValidationException::withMessages([
                'username' => ['This account has been deactivated.'],
            ]);
        }

        if (! Hash::check($request->input('password'), $user->getAuthPassword())) {
            $this->recordFailure($user);
            $this->failGeneric();
        }

        // A successful login clears the counter; otherwise a user who fails
        // four times and then succeeds stays one mistake from a lockout.
        $this->asUserFacility($user, fn () => $user->forceFill([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save());

        $token = $user->createToken('auth-token')->plainTextToken;

        AuditLogger::record('login', 'app_user', $user->id);

        return response()->json([
            'user' => $this->presentUser($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->user()->currentAccessToken()->delete();

        AuditLogger::record('logout', 'app_user', $user->id);

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->presentUser($request->user())]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'access_token' => $user->createToken('auth-token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Count a failed attempt and lock the account once the threshold is passed.
     */
    private function recordFailure(User $user): void
    {
        $attempts = (int) $user->failed_attempts + 1;

        $this->asUserFacility($user, fn () => $user->forceFill([
            'failed_attempts' => $attempts,
            'locked_until' => $attempts >= self::MAX_ATTEMPTS
                ? now()->addMinutes(self::LOCKOUT_MINUTES)
                : null,
        ])->save());

        AuditLogger::record('login_failed', 'app_user', $user->id);
    }

    /**
     * Run a write with the user's facility as the RLS context.
     *
     * The `auth_lookup` policy is SELECT-only, so during login there is no
     * facility context and any UPDATE to app_user matches ZERO ROWS — silently,
     * because an UPDATE that matches nothing is not an error. That silently
     * broke both the failed-attempt counter (so lockout never triggered, and
     * the endpoint was open to brute force) and `last_login_at`.
     *
     * Once the user has been found their facility is known, so the context can
     * be set for the write. Transaction-scoped, so it is pooler-safe and does
     * not leak onto the next request sharing the connection.
     */
    private function asUserFacility(User $user, callable $write): void
    {
        DB::transaction(function () use ($user, $write) {
            DB::statement('SELECT set_config(?, ?, true)',
                ['app.facility_id', (string) $user->facility_id]);

            $write();
        });
    }

    /**
     * @throws ValidationException
     */
    private function failGeneric(): never
    {
        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * The client needs the facility and permissions to render correctly, but
     * never the password hash.
     *
     * @return array<string, mixed>
     */
    private function presentUser(User $user): array
    {
        $facility = DB::table('facility')->where('id', $user->facility_id)->first();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'display_name' => $user->display_name,
            'email' => $user->email,
            // Load-bearing on the client too: it drives provider matching and
            // the same-gender scheduling default.
            'gender' => $user->gender,
            'preferred_locale' => $user->preferred_locale,
            'facility' => $facility ? [
                'id' => $facility->id,
                'name_local' => $facility->name_local,
                'name_latin' => $facility->name_latin,
            ] : null,
            'roles' => $user->roles()->pluck('code'),
            'permissions' => $user->permissions(),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ];
    }
}
