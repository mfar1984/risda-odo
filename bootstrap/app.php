<?php

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
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdministrator::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'permission_any' => \App\Http\Middleware\PermissionAnyMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
            'api.cors' => \App\Http\Middleware\ApiCorsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
