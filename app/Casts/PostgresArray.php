<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Casts a native PostgreSQL array column (text[]) to and from a PHP array.
 *
 * Laravel's built-in `array` cast serializes to JSON, which Postgres rejects
 * for a real array column — `[]` is not a valid array literal, `{}` is. Using
 * jsonb instead would work, but a native array keeps GIN containment operators
 * (`@>`, `&&`) available, which is what makes "find everyone with this
 * honorific" an indexed query.
 */
class PostgresArray implements CastsAttributes
{
    /**
     * @return array<int, string>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || $value === '' || $value === '{}') {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $inner = trim((string) $value, '{}');

        if ($inner === '') {
            return [];
        }

        // Postgres quotes elements containing commas, braces, quotes or spaces.
        // str_getcsv handles the quoting and escaping rules correctly.
        return array_map(
            fn (string $item) => stripcslashes(trim($item, '"')),
            str_getcsv($inner, ',', '"', '\\')
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $items = is_array($value) ? $value : [$value];

        if ($items === []) {
            return '{}';
        }

        $escaped = array_map(function ($item): string {
            // Quote everything: always correct, and avoids deciding case by case
            // whether a given Dari string needs it.
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $item).'"';
        }, $items);

        return '{'.implode(',', $escaped).'}';
    }
}
