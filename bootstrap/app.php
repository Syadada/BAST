<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: function (\Illuminate\Http\Request $request) {
                return route('login');
            },
            users: function () {
                if (auth()->check() && auth()->user()->isSuperAdmin()) {
                    return route('admin.dashboard');
                }
                return route('opd.dashboard');
            }
        );

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'opd' => \App\Http\Middleware\OpdOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
