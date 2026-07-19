<?php

namespace App\Http\Middleware;

use App\Support\TextNormalizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Folds Dari/Pashto digits and strips invisible control characters from every
 * inbound string, before validation or any model sees it (ADR-007).
 *
 * Why this is global rather than per-field: users type on Afghan keyboards,
 * which produce Extended Arabic-Indic digits (U+06F0–U+06F9). If those reach
 * the database they break ORDER BY, break `WHERE age > 60`, break index
 * lookups, and create duplicate patients — MRN ۱۲۳۴ is not MRN 1234. Relying
 * on each controller to remember is exactly the kind of discipline that fails.
 *
 * This deliberately does NOT fold letters. Stored names stay verbatim
 * (ADR-005); letter folding happens only when building a *_search column.
 */
class NormalizeRequestText
{
    /**
     * Fields whose contents are meaningful byte-for-byte and must not be
     * touched. Passwords especially: normalizing whitespace or digits inside
     * one would silently change the credential.
     */
    private const SKIP = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'signature',
        '_token',
        'remember_token',
        'content_hash',
        'raw_data',        // GS1 barcode payload — GS separators are load-bearing
        'signed_manifestation',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if ($input !== []) {
            $request->merge($this->normalize($input));
        }

        return $next($request);
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    private function normalize(array $data, string $path = ''): array
    {
        foreach ($data as $key => $value) {
            $currentPath = $path === '' ? (string) $key : $path.'.'.$key;

            if (is_array($value)) {
                $data[$key] = $this->normalize($value, $currentPath);

                continue;
            }

            if (! is_string($value)) {
                continue;
            }

            // Match on the leaf key so that nested paths such as
            // items.0.password are skipped too.
            if (in_array((string) $key, self::SKIP, true)) {
                continue;
            }

            $data[$key] = TextNormalizer::normalizeInput($value);
        }

        return $data;
    }
}
