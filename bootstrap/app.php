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
        $middleware->alias([
            'check.lesson.access' => \App\Http\Middleware\CheckLessonAccess::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'check.banned' => \App\Http\Middleware\CheckBannedUser::class,
            'mfa.verified' => \App\Http\Middleware\EnsureMfaVerified::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\TrackUserActivity::class,
            \App\Http\Middleware\CheckBannedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
