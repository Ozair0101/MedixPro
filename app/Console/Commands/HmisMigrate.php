<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Runs migrations as the schema owner.
 *
 * `php artisan migrate` fails by design: the application connects as
 * `hmis_app`, which has no CREATE rights on the public schema. That is not an
 * oversight to work around — it is the control that keeps the application from
 * owning its own tables.
 *
 *   A superuser bypasses Row-Level Security unconditionally.
 *   A table's OWNER bypasses RLS unless FORCE is set.
 *
 * If the app connected as the owner, every tenant-isolation policy would be
 * silently inert, with no error to reveal it. So DDL runs on a separate
 * connection with owner credentials, and this command is the front door.
 *
 *   php artisan hmis:migrate              run pending migrations
 *   php artisan hmis:migrate --baseline   mark them as run WITHOUT executing,
 *                                         for a database already built by psql
 *   php artisan hmis:migrate --fresh      drop everything and rebuild (local only)
 */
class HmisMigrate extends Command
{
    protected $signature = 'hmis:migrate
        {--baseline : Record migrations as run without executing them}
        {--fresh : Drop the schema and rebuild from scratch}
        {--seed : Run seeders afterwards}
        {--force : Skip the production confirmation}';

    protected $description = 'Run database migrations as the schema owner';

    private const CONNECTION = 'pgsql_migrate';

    public function handle(): int
    {
        if (! $this->checkCredentials()) {
            return self::FAILURE;
        }

        if ($this->option('baseline')) {
            return $this->baseline();
        }

        if ($this->option('fresh')) {
            return $this->fresh();
        }

        // A database built with psql already has the tables but no migration
        // rows, so a plain migrate would fail on "already exists". Detect that
        // and point at --baseline rather than letting it fail confusingly.
        if ($this->schemaExists() && ! $this->migrationsRecorded()) {
            $this->warn('The schema already exists but no migrations are recorded.');
            $this->line('This happens when the database was built directly with psql.');
            $this->newLine();
            $this->line('  php artisan hmis:migrate --baseline    record them as run');
            $this->line('  php artisan hmis:migrate --fresh       rebuild from scratch (destroys data)');

            return self::FAILURE;
        }

        $this->info('Running migrations as '.$this->owner().'…');

        $exit = $this->call('migrate', [
            '--database' => self::CONNECTION,
            '--force' => true,
        ]);

        if ($exit === self::SUCCESS && $this->option('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }

        return $exit;
    }

    /**
     * Record every migration as run, without executing it.
     *
     * For a database whose schema was applied with psql — the normal path for
     * an on-premise install where a DBA loads database/schema/*.sql directly.
     */
    private function baseline(): int
    {
        if (! $this->schemaExists()) {
            $this->error('No schema found to baseline. Run `php artisan hmis:migrate` instead.');

            return self::FAILURE;
        }

        $connection = DB::connection(self::CONNECTION);

        $connection->getSchemaBuilder()->hasTable('migrations')
            || $this->call('migrate:install', ['--database' => self::CONNECTION]);

        $files = collect(glob(database_path('migrations/*.php')))
            ->map(fn ($path) => basename($path, '.php'))
            ->sort()
            ->values();

        $already = $connection->table('migrations')->pluck('migration')->all();
        $batch = (int) $connection->table('migrations')->max('batch') + 1;

        $recorded = 0;

        foreach ($files as $migration) {
            if (in_array($migration, $already, true)) {
                continue;
            }

            $connection->table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);

            $this->line("  recorded {$migration}");
            $recorded++;
        }

        $this->newLine();
        $this->info("Baselined: {$recorded} migrations recorded as already run.");
        $this->line('Future `php artisan hmis:migrate` runs will apply only new ones.');

        return self::SUCCESS;
    }

    private function fresh(): int
    {
        if (! app()->environment(['local', 'testing']) && ! $this->option('force')) {
            $this->error(
                'Refusing to drop the schema in the '.app()->environment().' environment. '
                .'This destroys all patient data. Restore from a backup instead.'
            );

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm(
            'This DROPS every table and all data in "'.config('database.connections.pgsql.database')
            .'". Continue?', false
        )) {
            return self::FAILURE;
        }

        $this->warn('Dropping schema…');

        DB::connection(self::CONNECTION)->unprepared(
            'DROP SCHEMA public CASCADE; CREATE SCHEMA public;'
        );

        // The role must be able to use the schema again after the recreate.
        DB::connection(self::CONNECTION)->unprepared(
            'GRANT USAGE ON SCHEMA public TO hmis_app;'
        );

        $exit = $this->call('migrate', [
            '--database' => self::CONNECTION,
            '--force' => true,
        ]);

        if ($exit === self::SUCCESS && $this->option('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }

        return $exit;
    }

    private function checkCredentials(): bool
    {
        try {
            DB::connection(self::CONNECTION)->getPdo();
        } catch (\Throwable $e) {
            $this->error('Cannot connect as the migration user.');
            $this->newLine();
            $this->line('Set these in .env — they are the DDL owner, not the app user:');
            $this->line('  DB_MIGRATE_USERNAME=postgres');
            $this->line('  DB_MIGRATE_PASSWORD=<owner password>');
            $this->newLine();
            $this->line('Reason: '.$e->getMessage());

            return false;
        }

        return true;
    }

    private function owner(): string
    {
        return (string) config('database.connections.'.self::CONNECTION.'.username');
    }

    private function schemaExists(): bool
    {
        return DB::connection(self::CONNECTION)->selectOne(
            "SELECT to_regclass('public.facility') IS NOT NULL AS present"
        )->present;
    }

    private function migrationsRecorded(): bool
    {
        $connection = DB::connection(self::CONNECTION);

        if (! $connection->getSchemaBuilder()->hasTable('migrations')) {
            return false;
        }

        return $connection->table('migrations')->exists();
    }
}
