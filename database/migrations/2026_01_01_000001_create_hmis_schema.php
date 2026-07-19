<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Installs the full HMIS schema.
 *
 * WHY THIS RUNS SQL FILES RATHER THAN Schema::create()
 * ----------------------------------------------------
 * The schema depends on PostgreSQL features Laravel's schema builder cannot
 * express, and each one is load-bearing rather than decorative:
 *
 *   EXCLUDE USING gist   two patients cannot occupy one bed; tariff prices
 *                        cannot overlap in time; accounting periods cannot
 *                        overlap. 18 of these.
 *   Row-Level Security   145 policies enforcing tenant isolation below the
 *                        application, so a forgotten WHERE cannot leak data.
 *   Triggers             append-only ledgers, per-currency journal balancing.
 *   Generated columns    qty_available, BMI, invoice totals.
 *   Partitioning         audit_log by month.
 *   Partial indexes      UNIQUE(national_id) WHERE national_id IS NOT NULL.
 *
 * Rewriting those as DB::statement() strings inside Schema::create() would give
 * the appearance of idiomatic migrations while actually being the same raw SQL,
 * just harder to read and diff. Keeping database/schema/*.sql as the source of
 * truth and loading it here gives `php artisan migrate` and `migrate:rollback`
 * without pretending the schema is something it is not.
 *
 * The .sql files are versioned, reviewable, and executable directly with psql —
 * which matters for an on-premise deployment where a DBA may need to inspect or
 * repair the schema without a PHP runtime.
 */
return new class extends Migration
{
    /** Load order matters: later files reference earlier tables. */
    private const FILES = [
        '10-platform.sql',
        '20-identity.sql',
        '30-encounter-adt.sql',
        '40-clinical.sql',
        '50-diagnostics.sql',
        '60-pharmacy-supply.sql',
        '70-finance.sql',
        '80-workforce-support.sql',
        '90-reporting.sql',
        '99-grants.sql',
    ];

    public function up(): void
    {
        $this->assertPostgres();
        $this->assertApplicationRoleExists();

        foreach (self::FILES as $file) {
            $path = $this->schemaPath($file);

            if (! is_file($path)) {
                throw new RuntimeException("Schema file not found: {$path}");
            }

            // unprepared(): these files contain multiple statements, dollar-quoted
            // function bodies and DO blocks, none of which survive prepared
            // statement handling.
            DB::unprepared(file_get_contents($path));
        }
    }

    public function down(): void
    {
        $this->assertPostgres();

        // Dropping and recreating the schema is the only sane rollback for a
        // structure with this many interdependencies. It is destructive by
        // definition, which is why it refuses to run outside local/testing.
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException(
                'Rolling back the HMIS schema drops every table and all patient '
                .'data. Refusing to run in the '.app()->environment().' environment. '
                .'Restore from a backup instead.'
            );
        }

        DB::unprepared('DROP SCHEMA public CASCADE; CREATE SCHEMA public;');
    }

    private function schemaPath(string $file): string
    {
        return base_path('../database/schema/'.$file);
    }

    private function assertPostgres(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            throw new RuntimeException(
                'This schema requires PostgreSQL 16+. The current connection is '
                .DB::connection()->getDriverName().'. See docs/00-architecture-decisions.md '
                .'(ADR-001) for why MySQL cannot express these constraints.'
            );
        }
    }

    /**
     * 99-grants.sql grants to `hmis_app`, and the role must already exist.
     *
     * It is created by the DBA rather than by a migration on purpose: the
     * application must NOT connect as the role that owns its tables, because a
     * table owner bypasses Row-Level Security unless FORCE is set, and a
     * superuser bypasses it unconditionally.
     */
    private function assertApplicationRoleExists(): void
    {
        $exists = DB::selectOne("SELECT 1 AS ok FROM pg_roles WHERE rolname = 'hmis_app'");

        if (! $exists) {
            throw new RuntimeException(
                "The 'hmis_app' role does not exist. Create it first:\n\n"
                ."    CREATE ROLE hmis_app LOGIN PASSWORD '<password>';\n\n"
                .'It must be a plain role — never a superuser and never the table '
                .'owner — or every Row-Level Security policy is silently inert.'
            );
        }
    }
};
