<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Role as RoleMiddleware;
use App\Http\Middleware\MyTokenMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
        'web/*',
        'telegram/*',
        ]);
        $middleware->alias([
            'auth' => AuthMiddleware::class,
            'admin' => AdminMiddleware::class,
            'role' => RoleMiddleware::class,
            'mytoken' => MyTokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
