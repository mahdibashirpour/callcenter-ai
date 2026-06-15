<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('app')
                ->name('employer.')
                ->group(base_path('routes/employer.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('workspace')
                ->name('employee.')
                ->group(base_path('routes/employee.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'employer' => \App\Http\Middleware\EnsureEmployer::class,
            'employee' => \App\Http\Middleware\EnsureEmployee::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ImpersonationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
