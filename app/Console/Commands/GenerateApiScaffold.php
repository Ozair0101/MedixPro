<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generates models, controllers, form requests and API resources by
 * introspecting the live PostgreSQL schema.
 *
 * The DATABASE is the source of truth, not a hand-maintained list. Postgres has
 * already parsed the DDL, so column types, nullability, defaults, foreign keys,
 * CHECK enumerations and primary keys are read from the catalog rather than
 * guessed — which is what keeps 200-plus generated files consistent with the
 * schema instead of drifting from it.
 *
 * Hand-written files are NEVER overwritten unless --force is passed. The
 * generator produces a correct baseline; domain logic that has been added on
 * top of it stays.
 *
 *   php artisan hmis:scaffold --module=clinical
 *   php artisan hmis:scaffold --table=patient --force
 *   php artisan hmis:scaffold --all --dry-run
 */
class GenerateApiScaffold extends Command
{
    protected $signature = 'hmis:scaffold
        {--table=* : Specific tables}
        {--module= : platform|identity|encounter|clinical|diagnostics|pharmacy|finance|workforce|reporting}
        {--all : Every table in the schema}
        {--force : Overwrite existing files}
        {--dry-run : List what would be generated}
        {--only= : models|controllers|requests|resources (default: all)}';

    protected $description = 'Generate models, controllers, requests and resources from the database schema';

    /**
     * Tables owned by the framework or by reference data, which get a model but
     * no CRUD endpoints.
     */
    private const NO_CRUD = [
        'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'sessions', 'password_reset_tokens', 'personal_access_tokens', 'users',
        'audit_log', 'audit_log_default', 'sync_event', 'stock_ledger',
        'journal_line', 'receivable_entry', 'controlled_drug_register',
        'patient_merge_detail', 'encounter_status_history',
    ];

    /**
     * Append-only tables. They get a read-only controller: a generated CRUD
     * endpoint that offers UPDATE and DELETE on a ledger would be a loaded gun,
     * even though the database triggers would refuse.
     */
    private const READ_ONLY = [
        'audit_log', 'stock_ledger', 'journal_line', 'journal_entry',
        'receivable_entry', 'controlled_drug_register', 'adt_event',
        'electronic_signature', 'fx_rate', 'sync_event',
    ];

    /** Which schema file each table came from, for module filtering. */
    private array $moduleMap = [];

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('Scaffolding introspects PostgreSQL. Point DB_CONNECTION at pgsql.');

            return self::FAILURE;
        }

        $this->buildModuleMap();

        $tables = $this->resolveTables();

        if ($tables === []) {
            $this->error('No tables matched. Use --all, --module=<name> or --table=<name>.');

            return self::FAILURE;
        }

        $only = $this->option('only') ? explode(',', $this->option('only')) : null;
        $wants = fn (string $kind) => $only === null || in_array($kind, $only, true);

        $counts = ['models' => 0, 'controllers' => 0, 'requests' => 0, 'resources' => 0];

        foreach ($tables as $table) {
            $meta = $this->introspect($table);

            if ($wants('models') && $this->writeModel($meta)) {
                $counts['models']++;
            }

            if (in_array($table, self::NO_CRUD, true)) {
                continue;
            }

            if ($wants('requests') && $this->writeRequest($meta)) {
                $counts['requests']++;
            }
            if ($wants('resources') && $this->writeResource($meta)) {
                $counts['resources']++;
            }
            if ($wants('controllers') && $this->writeController($meta)) {
                $counts['controllers']++;
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d models, %d controllers, %d requests, %d resources across %d tables.',
            $this->option('dry-run') ? 'Would generate' : 'Generated',
            $counts['models'], $counts['controllers'],
            $counts['requests'], $counts['resources'], count($tables)
        ));

        if (! $this->option('dry-run')) {
            $this->line('Run `php artisan hmis:scaffold-routes` to register the endpoints.');
        }

        return self::SUCCESS;
    }

    // ---------------------------------------------------------------------
    // Introspection
    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function introspect(string $table): array
    {
        $columns = DB::select("
            SELECT c.column_name, c.data_type, c.udt_name, c.is_nullable,
                   c.column_default, c.character_maximum_length,
                   c.numeric_precision, c.numeric_scale,
                   c.is_generated, c.identity_generation
              FROM information_schema.columns c
             WHERE c.table_schema = 'public' AND c.table_name = ?
             ORDER BY c.ordinal_position
        ", [$table]);

        $primaryKey = DB::select('
            SELECT a.attname
              FROM pg_index i
              JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
             WHERE i.indrelid = ?::regclass AND i.indisprimary
        ', [$table]);

        $foreignKeys = DB::select("
            SELECT kcu.column_name, ccu.table_name AS foreign_table,
                   ccu.column_name AS foreign_column
              FROM information_schema.table_constraints tc
              JOIN information_schema.key_column_usage kcu
                ON kcu.constraint_name = tc.constraint_name
              JOIN information_schema.constraint_column_usage ccu
                ON ccu.constraint_name = tc.constraint_name
             WHERE tc.constraint_type = 'FOREIGN KEY'
               AND tc.table_schema = 'public' AND tc.table_name = ?
        ", [$table]);

        // CHECK constraints of the form `col IN ('a','b')` are the schema's
        // enums; reading them here means validation rules stay in step with the
        // database instead of being retyped and drifting.
        $checks = DB::select("
            SELECT con.conname, pg_get_constraintdef(con.oid) AS definition
              FROM pg_constraint con
             WHERE con.conrelid = ?::regclass AND con.contype = 'c'
        ", [$table]);

        return [
            'table' => $table,
            'class' => $this->studly($table),
            'columns' => $columns,
            'primary_key' => $primaryKey[0]->attname ?? 'id',
            'primary_is_uuid' => $this->columnType($columns, $primaryKey[0]->attname ?? 'id') === 'uuid',
            'foreign_keys' => $foreignKeys,
            'enums' => $this->parseEnums($checks),
            'has_timestamps' => $this->hasColumn($columns, 'created_at')
                && $this->hasColumn($columns, 'updated_at'),
            'has_facility' => $this->hasColumn($columns, 'facility_id'),
            'read_only' => in_array($table, self::READ_ONLY, true),
            'module' => $this->moduleMap[$table] ?? 'other',
        ];
    }

    /**
     * Extract `col IN ('a','b','c')` from CHECK definitions.
     *
     * @return array<string, array<int, string>>
     */
    private function parseEnums(array $checks): array
    {
        $enums = [];

        foreach ($checks as $check) {
            if (! preg_match(
                '/\(?\(?([a-z_]+)\)?::text\s*=\s*ANY\s*\(\s*\(?ARRAY\[(.*?)\]/is',
                $check->definition, $m
            )) {
                continue;
            }

            preg_match_all("/'([^']+)'/", $m[2], $values);

            if ($values[1] !== []) {
                $enums[$m[1]] = array_values(array_unique($values[1]));
            }
        }

        return $enums;
    }

    // ---------------------------------------------------------------------
    // Writers
    // ---------------------------------------------------------------------

    private function writeModel(array $meta): bool
    {
        $path = app_path("Models/{$meta['class']}.php");

        if (! $this->shouldWrite($path)) {
            return false;
        }

        $fillable = [];
        $casts = [];

        foreach ($meta['columns'] as $column) {
            $name = $column->column_name;

            // Never fillable: server-assigned identity, row metadata, or a
            // value the database computes.
            if (in_array($name, [$meta['primary_key'], 'created_at', 'updated_at'], true)
                || $column->is_generated === 'ALWAYS'
                || $column->identity_generation !== null) {
                continue;
            }

            $fillable[] = $name;

            if ($cast = $this->castFor($column)) {
                $casts[$name] = $cast;
            }
        }

        $relations = $this->relationMethods($meta);

        $uses = ['Illuminate\Database\Eloquent\Model'];
        $traits = [];

        if ($meta['has_facility']) {
            $uses[] = 'App\Models\Concerns\BelongsToFacility';
            $traits[] = 'BelongsToFacility';
        }
        if ($meta['primary_is_uuid'] && $meta['primary_key'] === 'id') {
            $uses[] = 'Illuminate\Database\Eloquent\Concerns\HasUuids';
            $traits[] = 'HasUuids';
        }
        if ($relations !== '') {
            $uses[] = 'Illuminate\Database\Eloquent\Relations\BelongsTo';
        }

        sort($uses);
        sort($traits);

        $body = "<?php\n\nnamespace App\\Models;\n\n";
        foreach ($uses as $use) {
            $body .= "use {$use};\n";
        }
        $body .= "\n/**\n * Generated from the `{$meta['table']}` table.\n";
        if ($meta['read_only']) {
            $body .= " *\n * APPEND-ONLY. Database triggers reject UPDATE and DELETE; do not add\n"
                  ." * mutation methods here. Corrections are new rows, not edits.\n";
        }
        $body .= " */\nclass {$meta['class']} extends Model\n{\n";

        if ($traits !== []) {
            $body .= '    use '.implode(', ', $traits).";\n\n";
        }

        $body .= "    protected \$table = '{$meta['table']}';\n\n";

        if ($meta['primary_key'] !== 'id') {
            $body .= "    protected \$primaryKey = '{$meta['primary_key']}';\n\n";
        }
        if ($meta['primary_is_uuid'] || $meta['primary_key'] !== 'id') {
            $body .= "    public \$incrementing = false;\n\n";
            $body .= "    protected \$keyType = 'string';\n\n";
        }
        if (! $meta['has_timestamps']) {
            $body .= "    public \$timestamps = false;\n\n";
        }

        $body .= "    protected \$fillable = [\n";
        foreach (array_chunk($fillable, 4) as $chunk) {
            $body .= "        '".implode("', '", $chunk)."',\n";
        }
        $body .= "    ];\n";

        if ($casts !== []) {
            $body .= "\n    protected \$casts = [\n";
            foreach ($casts as $col => $cast) {
                $body .= "        '{$col}' => '{$cast}',\n";
            }
            $body .= "    ];\n";
        }

        if ($meta['enums'] !== []) {
            $body .= "\n    /** Values the database CHECK constraints permit. */\n";
            $body .= "    public const ENUMS = [\n";
            foreach ($meta['enums'] as $col => $values) {
                $body .= "        '{$col}' => ['".implode("', '", $values)."'],\n";
            }
            $body .= "    ];\n";
        }

        $body .= $relations."}\n";

        return $this->put($path, $body);
    }

    private function relationMethods(array $meta): string
    {
        $out = '';
        $seen = [];

        foreach ($meta['foreign_keys'] as $fk) {
            $target = $this->studly($fk->foreign_table);
            $method = Str::camel(Str::replaceLast('_id', '', $fk->column_name));

            if (isset($seen[$method]) || $method === '') {
                continue;
            }
            $seen[$method] = true;

            $out .= "\n    public function {$method}(): BelongsTo\n    {\n"
                 ."        return \$this->belongsTo({$target}::class, '{$fk->column_name}');\n"
                 ."    }\n";
        }

        return $out;
    }

    private function writeRequest(array $meta): bool
    {
        $path = app_path("Http/Requests/Store{$meta['class']}Request.php");

        if (! $this->shouldWrite($path)) {
            return false;
        }

        $rules = [];

        foreach ($meta['columns'] as $column) {
            $name = $column->column_name;

            if (in_array($name, [$meta['primary_key'], 'created_at', 'updated_at', 'facility_id'], true)
                || $column->is_generated === 'ALWAYS'
                || $column->identity_generation !== null) {
                continue;
            }

            $rules[$name] = $this->rulesFor($column, $meta);
        }

        $body = "<?php\n\nnamespace App\\Http\\Requests;\n\n"
            ."use Illuminate\\Foundation\\Http\\FormRequest;\n"
            ."use Illuminate\\Validation\\Rule;\n\n"
            ."/**\n * Validation for `{$meta['table']}`.\n *\n"
            ." * Rules are derived from the database: nullability, lengths, numeric\n"
            ." * precision, foreign keys and CHECK enumerations all come from the catalog,\n"
            ." * so they cannot drift from the constraints that actually apply.\n"
            ." *\n * facility_id is deliberately absent — it is server-side tenancy state set\n"
            ." * from the authenticated user, never accepted from a client.\n */\n"
            ."class Store{$meta['class']}Request extends FormRequest\n{\n"
            ."    public function authorize(): bool\n    {\n        return true;\n    }\n\n"
            ."    public function rules(): array\n    {\n        return [\n";

        foreach ($rules as $col => $rule) {
            $body .= "            '{$col}' => [{$rule}],\n";
        }

        $body .= "        ];\n    }\n}\n";

        return $this->put($path, $body);
    }

    private function writeResource(array $meta): bool
    {
        $path = app_path("Http/Resources/{$meta['class']}Resource.php");

        if (! $this->shouldWrite($path)) {
            return false;
        }

        $body = "<?php\n\nnamespace App\\Http\\Resources;\n\n"
            ."use Illuminate\\Http\\Request;\n"
            ."use Illuminate\\Http\\Resources\\Json\\JsonResource;\n\n"
            ."/**\n * API representation of `{$meta['table']}`.\n *\n"
            ." * facility_id is omitted: it is tenancy state derived from the\n"
            ." * authenticated user, and echoing it back invites clients to treat it as\n"
            ." * something they may set.\n */\n"
            ."class {$meta['class']}Resource extends JsonResource\n{\n"
            ."    public function toArray(Request \$request): array\n    {\n"
            ."        return [\n";

        foreach ($meta['columns'] as $column) {
            $name = $column->column_name;

            if ($name === 'facility_id') {
                continue;
            }

            $body .= match (true) {
                str_ends_with($name, '_at') => "            '{$name}' => \$this->{$name}?->toIso8601String(),\n",
                default => "            '{$name}' => \$this->{$name},\n",
            };
        }

        $body .= "        ];\n    }\n}\n";

        return $this->put($path, $body);
    }

    private function writeController(array $meta): bool
    {
        $path = app_path("Http/Controllers/Api/{$meta['class']}Controller.php");

        if (! $this->shouldWrite($path)) {
            return false;
        }

        $class = $meta['class'];
        $var = Str::camel($class);
        $readOnly = $meta['read_only'];

        $body = "<?php\n\nnamespace App\\Http\\Controllers\\Api;\n\n"
            ."use App\\Http\\Controllers\\Controller;\n"
            ."use App\\Http\\Requests\\Store{$class}Request;\n"
            ."use App\\Http\\Resources\\{$class}Resource;\n"
            ."use App\\Models\\{$class};\n"
            ."use App\\Support\\AuditLogger;\n"
            ."use Illuminate\\Http\\JsonResponse;\n"
            ."use Illuminate\\Http\\Request;\n\n"
            ."/**\n * CRUD for `{$meta['table']}`.\n";

        if ($readOnly) {
            $body .= " *\n * READ-ONLY. This table is append-only at the database level; the write\n"
                  ." * endpoints are omitted rather than offered and rejected.\n";
        }

        $body .= " */\nclass {$class}Controller extends Controller\n{\n"
            ."    public function index(Request \$request): JsonResponse\n    {\n"
            ."        \$perPage = min((int) \$request->query('per_page', 25), 100);\n\n"
            ."        // RLS scopes this to the caller's facility; no WHERE needed here.\n"
            ."        \$rows = {$class}::query()->paginate(\$perPage);\n\n"
            ."        return response()->json([\n"
            ."            'data' => {$class}Resource::collection(\$rows->items()),\n"
            ."            'meta' => [\n"
            ."                'current_page' => \$rows->currentPage(),\n"
            ."                'last_page' => \$rows->lastPage(),\n"
            ."                'per_page' => \$rows->perPage(),\n"
            ."                'total' => \$rows->total(),\n"
            ."            ],\n"
            ."        ]);\n    }\n\n"
            ."    public function show(string \$id): JsonResponse\n    {\n"
            ."        \${$var} = {$class}::findOrFail(\$id);\n\n"
            ."        return response()->json(['data' => new {$class}Resource(\${$var})]);\n"
            ."    }\n";

        if (! $readOnly) {
            $body .= "\n    public function store(Store{$class}Request \$request): JsonResponse\n    {\n"
                ."        \${$var} = {$class}::create(\$request->validated());\n\n"
                ."        AuditLogger::record('create', '{$meta['table']}', (string) \${$var}->getKey());\n\n"
                ."        return response()->json(\n"
                ."            ['data' => new {$class}Resource(\${$var})], 201\n"
                ."        );\n    }\n\n"
                ."    public function update(Store{$class}Request \$request, string \$id): JsonResponse\n    {\n"
                ."        \${$var} = {$class}::findOrFail(\$id);\n"
                ."        \${$var}->update(\$request->validated());\n\n"
                ."        AuditLogger::record('update', '{$meta['table']}', \$id);\n\n"
                ."        return response()->json(['data' => new {$class}Resource(\${$var})]);\n"
                ."    }\n\n";

            $hasActive = $this->hasColumn($meta['columns'], 'is_active');

            $body .= "    public function destroy(string \$id): JsonResponse\n    {\n"
                ."        \${$var} = {$class}::findOrFail(\$id);\n\n";

            if ($hasActive) {
                $body .= "        // Deactivate rather than delete: clinical and financial records\n"
                      ."        // must stay resolvable for anything that already references them.\n"
                      ."        \${$var}->update(['is_active' => false]);\n\n"
                      ."        AuditLogger::record('delete', '{$meta['table']}', \$id);\n\n"
                      ."        return response()->json(['message' => 'Deactivated.']);\n";
            } else {
                $body .= "        \${$var}->delete();\n\n"
                      ."        AuditLogger::record('delete', '{$meta['table']}', \$id);\n\n"
                      ."        return response()->json(['message' => 'Deleted.']);\n";
            }

            $body .= "    }\n";
        }

        $body .= "}\n";

        return $this->put($path, $body);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function rulesFor(object $column, array $meta): string
    {
        $rules = [$column->is_nullable === 'YES' || $column->column_default !== null
            ? "'nullable'" : "'required'"];

        $name = $column->column_name;

        if (isset($meta['enums'][$name])) {
            $rules[] = "Rule::in(['".implode("', '", $meta['enums'][$name])."'])";

            return implode(', ', $rules);
        }

        foreach ($meta['foreign_keys'] as $fk) {
            if ($fk->column_name === $name) {
                $rules[] = "'exists:{$fk->foreign_table},{$fk->foreign_column}'";
                break;
            }
        }

        $rules[] = match ($column->udt_name) {
            'uuid' => "'uuid'",
            'int2', 'int4', 'int8' => "'integer'",
            'numeric', 'float4', 'float8' => "'numeric'",
            'bool' => "'boolean'",
            'date' => "'date'",
            'timestamptz', 'timestamp' => "'date'",
            'jsonb', 'json' => "'array'",
            default => "'string'",
        };

        if ($column->character_maximum_length) {
            $rules[] = "'max:{$column->character_maximum_length}'";
        }

        return implode(', ', array_unique($rules));
    }

    private function castFor(object $column): ?string
    {
        return match ($column->udt_name) {
            'bool' => 'boolean',
            'int2', 'int4', 'int8' => 'integer',
            'numeric', 'float4', 'float8' => 'float',
            'date' => 'date',
            'timestamptz', 'timestamp' => 'datetime',
            'jsonb', 'json' => 'array',
            default => null,
        };
    }

    private function columnType(array $columns, string $name): ?string
    {
        foreach ($columns as $column) {
            if ($column->column_name === $name) {
                return $column->udt_name;
            }
        }

        return null;
    }

    private function hasColumn(array $columns, string $name): bool
    {
        foreach ($columns as $column) {
            if ($column->column_name === $name) {
                return true;
            }
        }

        return false;
    }

    private function studly(string $table): string
    {
        return Str::studly(Str::singular($table));
    }

    private function shouldWrite(string $path): bool
    {
        if (file_exists($path) && ! $this->option('force')) {
            return false;
        }

        return true;
    }

    private function put(string $path, string $contents): bool
    {
        if ($this->option('dry-run')) {
            $this->line('  would write '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $path));

            return true;
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $contents);
        $this->line('  '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $path));

        return true;
    }

    /** @return array<int, string> */
    private function resolveTables(): array
    {
        if ($this->option('table')) {
            return $this->option('table');
        }

        $all = array_map(
            fn ($r) => $r->tablename,
            DB::select("
                SELECT tablename FROM pg_tables
                 WHERE schemaname = 'public' ORDER BY tablename
            ")
        );

        // Partitions of audit_log are not separate entities.
        $all = array_values(array_filter($all, fn ($t) => ! str_starts_with($t, 'audit_log_')));

        if ($module = $this->option('module')) {
            return array_values(array_filter(
                $all, fn ($t) => ($this->moduleMap[$t] ?? '') === $module
            ));
        }

        return $this->option('all') ? $all : [];
    }

    /** Map each table to the schema file that created it. */
    private function buildModuleMap(): void
    {
        $files = [
            '10-platform.sql' => 'platform',
            '20-identity.sql' => 'identity',
            '30-encounter-adt.sql' => 'encounter',
            '40-clinical.sql' => 'clinical',
            '50-diagnostics.sql' => 'diagnostics',
            '60-pharmacy-supply.sql' => 'pharmacy',
            '70-finance.sql' => 'finance',
            '80-workforce-support.sql' => 'workforce',
            '90-reporting.sql' => 'reporting',
        ];

        foreach ($files as $file => $module) {
            $path = base_path('../database/schema/'.$file);

            if (! is_file($path)) {
                continue;
            }

            preg_match_all(
                '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?([a-z_]+)/i',
                file_get_contents($path), $matches
            );

            foreach ($matches[1] as $table) {
                $this->moduleMap[$table] = $module;
            }
        }
    }
}
