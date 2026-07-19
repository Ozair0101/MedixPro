<?php

use App\Http\Middleware\NormalizeRequestText;
use App\Http\Middleware\SetFacilityContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum for SPA authentication - but we're using token-based, not session
        // Disable CSRF for API routes since we're using Bearer tokens
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Fold Dari/Pashto digits and strip bidi controls before validation or
        // any model sees the input (ADR-007). Global, because relying on each
        // controller to remember is exactly the discipline that fails: Persian
        // digits in the database break ORDER BY, break indexes, and create
        // duplicate patients (MRN ۱۲۳۴ is not MRN 1234).
        $middleware->api(prepend: [
            NormalizeRequestText::class,
        ]);

        // Sets the Postgres session variable that Row-Level Security reads
        // (ADR-002). Must run AFTER authentication, since it derives the
        // facility from the authenticated user and never from a request header.
        $middleware->alias([
            'facility' => SetFacilityContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
