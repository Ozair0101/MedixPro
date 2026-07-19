<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Emits TypeScript resource descriptors for the frontend, from the database.
 *
 * WHY DESCRIPTORS AND NOT 199 PAGE FILES
 * --------------------------------------
 * Generating a bespoke list-and-form component per resource produces thousands
 * of lines of near-identical JSX that nobody reads and everybody has to
 * maintain. One reviewed CRUD component driven by a generated descriptor means
 * a fix to table rendering, RTL handling or validation lands everywhere at once.
 *
 * The descriptors carry field types, labels, enum options and foreign keys read
 * from the PostgreSQL catalog, so the forms cannot drift from the constraints
 * the database actually enforces.
 *
 * Clinical screens that need real workflow — registration with
 * search-before-create, dispensing, the encounter timeline — are hand-written
 * and deliberately excluded here.
 */
class GenerateFrontendCrud extends Command
{
    protected $signature = 'hmis:scaffold-frontend
        {--path= : Path to the SPA (default ../Hospital-MIS)}
        {--dry-run}';

    protected $description = 'Generate TypeScript resource descriptors for the SPA';

    /** Hand-written screens; a generic CRUD form would be a downgrade. */
    private const HAND_WRITTEN = [
        'patient', 'encounter', 'dispense', 'visit', 'vitals', 'person',
    ];

    /** Framework and ledger tables that get no UI. */
    private const SKIP = [
        'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'sessions', 'password_reset_tokens', 'personal_access_tokens', 'users',
        'sync_event', 'sync_conflict', 'patient_merge_detail',
        'encounter_status_history', 'audit_log_default',
    ];

    /** Append-only: list and view only, never edit. */
    private const READ_ONLY = [
        'audit_log', 'stock_ledger', 'journal_line', 'journal_entry',
        'receivable_entry', 'controlled_drug_register', 'adt_event',
        'electronic_signature', 'fx_rate',
    ];

    /** Human-facing module names for grouping in the navigation. */
    private const MODULE_LABELS = [
        'platform' => 'Administration',
        'identity' => 'Patient Registry',
        'encounter' => 'Visits & Wards',
        'clinical' => 'Clinical',
        'diagnostics' => 'Diagnostics',
        'pharmacy' => 'Pharmacy & Stock',
        'finance' => 'Finance',
        'workforce' => 'Workforce & Support',
        'reporting' => 'Reporting',
    ];

    private array $moduleMap = [];

    public function handle(): int
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('Frontend scaffolding introspects PostgreSQL.');

            return self::FAILURE;
        }

        $spa = $this->option('path') ?: base_path('../Hospital-MIS');

        if (! is_dir($spa)) {
            $this->error("SPA not found at {$spa}");

            return self::FAILURE;
        }

        $this->buildModuleMap();

        $tables = collect(DB::select("
            SELECT tablename FROM pg_tables
             WHERE schemaname = 'public' ORDER BY tablename
        "))
            ->pluck('tablename')
            ->reject(fn ($t) => in_array($t, self::SKIP, true)
                || in_array($t, self::HAND_WRITTEN, true)
                || str_starts_with($t, 'audit_log_'))
            ->values();

        $descriptors = [];

        foreach ($tables as $table) {
            $descriptors[] = $this->describe($table);
        }

        $file = $this->render($descriptors);
        $target = $spa.'/src/generated/resources.ts';

        if ($this->option('dry-run')) {
            $this->line("Would write {$target} — ".count($descriptors).' resources.');

            return self::SUCCESS;
        }

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        file_put_contents($target, $file);

        $this->info('Wrote src/generated/resources.ts — '.count($descriptors).' resources.');

        return self::SUCCESS;
    }

    /** @return array<string, mixed> */
    private function describe(string $table): array
    {
        $columns = DB::select("
            SELECT column_name, udt_name, is_nullable, column_default,
                   character_maximum_length, is_generated, identity_generation
              FROM information_schema.columns
             WHERE table_schema = 'public' AND table_name = ?
             ORDER BY ordinal_position
        ", [$table]);

        $foreignKeys = collect(DB::select("
            SELECT kcu.column_name, ccu.table_name AS foreign_table
              FROM information_schema.table_constraints tc
              JOIN information_schema.key_column_usage kcu
                ON kcu.constraint_name = tc.constraint_name
              JOIN information_schema.constraint_column_usage ccu
                ON ccu.constraint_name = tc.constraint_name
             WHERE tc.constraint_type = 'FOREIGN KEY'
               AND tc.table_schema = 'public' AND tc.table_name = ?
        ", [$table]))->keyBy('column_name');

        $enums = $this->parseEnums(DB::select("
            SELECT pg_get_constraintdef(oid) AS definition
              FROM pg_constraint WHERE conrelid = ?::regclass AND contype = 'c'
        ", [$table]));

        $fields = [];

        foreach ($columns as $column) {
            $name = $column->column_name;

            // Server-owned: identity, tenancy and row metadata never appear on
            // a form.
            if (in_array($name, ['id', 'facility_id', 'created_at', 'updated_at'], true)
                || $column->is_generated === 'ALWAYS'
                || $column->identity_generation !== null) {
                continue;
            }

            $fields[] = [
                'name' => $name,
                'label' => $this->humanize($name),
                'type' => $this->fieldType($column, isset($enums[$name])),
                'required' => $column->is_nullable === 'NO' && $column->column_default === null,
                'maxLength' => $column->character_maximum_length,
                'options' => $enums[$name] ?? null,
                'relatedResource' => isset($foreignKeys[$name])
                    ? Str::kebab(Str::plural($foreignKeys[$name]->foreign_table))
                    : null,
            ];
        }

        return [
            'table' => $table,
            'name' => Str::kebab(Str::plural($table)),
            'label' => $this->humanize(Str::plural($table)),
            'module' => $this->moduleMap[$table] ?? 'other',
            'moduleLabel' => self::MODULE_LABELS[$this->moduleMap[$table] ?? ''] ?? 'Other',
            'readOnly' => in_array($table, self::READ_ONLY, true),
            'fields' => $fields,
            // First few text-ish fields make a sensible default table view.
            'listColumns' => collect($fields)
                ->filter(fn ($f) => in_array($f['type'], ['text', 'select', 'number', 'date'], true))
                ->take(5)->pluck('name')->values()->all(),
        ];
    }

    /** @return array<string, array<int, string>> */
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

    private function fieldType(object $column, bool $isEnum): string
    {
        if ($isEnum) {
            return 'select';
        }

        return match ($column->udt_name) {
            'bool' => 'boolean',
            'int2', 'int4', 'int8', 'numeric', 'float4', 'float8' => 'number',
            'date' => 'date',
            'timestamptz', 'timestamp' => 'datetime',
            'jsonb', 'json' => 'json',
            'uuid' => 'reference',
            'text' => 'textarea',
            default => 'text',
        };
    }

    private function humanize(string $value): string
    {
        return Str::of($value)->replace('_', ' ')->title()->toString();
    }

    private function render(array $descriptors): string
    {
        $json = json_encode($descriptors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES);

        return <<<TS
        /**
         * GENERATED FILE — DO NOT EDIT
         *
         * Regenerate with: php artisan hmis:scaffold-frontend
         *
         * Resource descriptors introspected from the PostgreSQL catalog: field
         * types, requiredness, lengths, enum options and foreign keys all come
         * from the constraints the database actually enforces, so a form cannot
         * drift from what will be accepted on save.
         *
         * These drive the shared <CrudPage> component rather than generating a
         * component per resource. Clinical screens that need real workflow —
         * registration with search-before-create, dispensing, the encounter
         * timeline — are hand-written and excluded from this file.
         *
         * Generated {$this->today()} · {$this->count($descriptors)} resources.
         */

        export type FieldType =
          | 'text' | 'textarea' | 'number' | 'boolean'
          | 'date' | 'datetime' | 'select' | 'reference' | 'json';

        export interface ResourceField {
          name: string;
          label: string;
          type: FieldType;
          required: boolean;
          maxLength: number | null;
          /** Values the database CHECK constraint permits. */
          options: string[] | null;
          /** Endpoint to load options from, for foreign keys. */
          relatedResource: string | null;
        }

        export interface ResourceDescriptor {
          table: string;
          /** URL segment and API path, e.g. 'stock-items'. */
          name: string;
          label: string;
          module: string;
          moduleLabel: string;
          /** Append-only in the database: list and view only. */
          readOnly: boolean;
          fields: ResourceField[];
          listColumns: string[];
        }

        export const RESOURCES: ResourceDescriptor[] = {$json};

        export const RESOURCE_BY_NAME: Record<string, ResourceDescriptor> =
          Object.fromEntries(RESOURCES.map((r) => [r.name, r]));

        /** Resources grouped for the navigation sidebar. */
        export const RESOURCES_BY_MODULE = RESOURCES.reduce<
          Record<string, ResourceDescriptor[]>
        >((acc, resource) => {
          (acc[resource.moduleLabel] ??= []).push(resource);
          return acc;
        }, {});

        TS;
    }

    private function today(): string
    {
        return now()->toDateString();
    }

    private function count(array $descriptors): int
    {
        return count($descriptors);
    }

    private function buildModuleMap(): void
    {
        $files = [
            '10-platform.sql' => 'platform', '20-identity.sql' => 'identity',
            '30-encounter-adt.sql' => 'encounter', '40-clinical.sql' => 'clinical',
            '50-diagnostics.sql' => 'diagnostics', '60-pharmacy-supply.sql' => 'pharmacy',
            '70-finance.sql' => 'finance', '80-workforce-support.sql' => 'workforce',
            '90-reporting.sql' => 'reporting',
        ];

        foreach ($files as $file => $module) {
            $path = base_path('../database/schema/'.$file);

            if (! is_file($path)) {
                continue;
            }

            preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?([a-z_]+)/i',
                file_get_contents($path), $matches);

            foreach ($matches[1] as $table) {
                $this->moduleMap[$table] = $module;
            }
        }
    }
}
