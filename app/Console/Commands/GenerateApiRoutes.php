<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Writes routes/api_generated.php from the generated controllers.
 *
 * Generated routes live in their own file, included by routes/api.php, so that
 * hand-written endpoints (registration, dispensing, merge) are never clobbered
 * by regeneration. Rerunning the generator rewrites only this file.
 */
class GenerateApiRoutes extends Command
{
    protected $signature = 'hmis:scaffold-routes {--dry-run}';

    protected $description = 'Register API routes for every generated controller';

    /**
     * Endpoints with hand-written controllers. Excluded so the generator does
     * not shadow the domain logic with plain CRUD.
     */
    private const HAND_WRITTEN = [
        'Patient', 'Encounter', 'Dispense', 'Visit', 'Vitals',
    ];

    /** Append-only: no store/update/destroy routes. */
    private const READ_ONLY = [
        'AuditLog', 'StockLedger', 'JournalLine', 'JournalEntry',
        'ReceivableEntry', 'ControlledDrugRegister', 'AdtEvent',
        'ElectronicSignature', 'FxRate', 'SyncEvent',
    ];

    public function handle(): int
    {
        $dir = app_path('Http/Controllers/Api');

        if (! is_dir($dir)) {
            $this->error('No generated controllers found. Run hmis:scaffold first.');

            return self::FAILURE;
        }

        $controllers = collect(scandir($dir))
            ->filter(fn ($f) => str_ends_with($f, 'Controller.php'))
            ->map(fn ($f) => Str::replaceLast('Controller.php', '', $f))
            ->reject(fn ($name) => in_array($name, self::HAND_WRITTEN, true))
            ->sort()
            ->values();

        $body = "<?php\n\n"
            ."/*\n"
            ."|--------------------------------------------------------------------------\n"
            ."| Generated API routes — DO NOT EDIT\n"
            ."|--------------------------------------------------------------------------\n"
            ."|\n"
            ."| Regenerate with: php artisan hmis:scaffold-routes\n"
            ."|\n"
            ."| Hand-written endpoints live in routes/api.php and are excluded here, so\n"
            ."| regeneration never overwrites domain logic with plain CRUD.\n"
            ."|\n"
            ."| Every route below already sits inside the v1 group in api.php, which\n"
            ."| applies auth:sanctum and the facility middleware. The facility middleware\n"
            ."| sets the PostgreSQL session variable that Row-Level Security reads —\n"
            ."| without it queries return nothing, which is the correct behaviour for an\n"
            ."| unattributed request.\n"
            ."|\n"
            ."| Generated ".now()->toDateString().", ".$controllers->count()." resources.\n"
            ."*/\n\n"
            ."use Illuminate\\Support\\Facades\\Route;\n\n";

        foreach ($controllers as $name) {
            $slug = Str::kebab(Str::plural($name));
            $class = "App\\Http\\Controllers\\Api\\{$name}Controller";

            if (in_array($name, self::READ_ONLY, true)) {
                $body .= "// {$name}: append-only at the database level.\n";
                $body .= "Route::get('{$slug}', [\\{$class}::class, 'index']);\n";
                $body .= "Route::get('{$slug}/{id}', [\\{$class}::class, 'show']);\n\n";

                continue;
            }

            $body .= "Route::apiResource('{$slug}', \\{$class}::class);\n";
        }

        $path = base_path('routes/api_generated.php');

        if ($this->option('dry-run')) {
            $this->line("Would write {$path} with {$controllers->count()} resources.");

            return self::SUCCESS;
        }

        file_put_contents($path, $body);

        $this->info("Wrote routes/api_generated.php — {$controllers->count()} resources.");
        $this->line('Ensure routes/api.php includes it inside the v1 group.');

        return self::SUCCESS;
    }
}
