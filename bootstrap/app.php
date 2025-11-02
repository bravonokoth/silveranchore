<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

// ADD THIS LINE
use App\Http\Middleware\SyncGuestCart;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // -------------------------------------------------
        //  WEB MIDDLEWARE GROUP – runs on every page
        // -------------------------------------------------
        $middleware->web(append: [
            SyncGuestCart::class,   // <-- THIS FIXES GUEST CART SYNC
        ]);

        // -------------------------------------------------
        //  MIDDLEWARE ALIASES (for route definitions)
        // -------------------------------------------------
        $middleware->alias([
            'auth'               => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic'         => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'      => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'              => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm'   => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'             => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'           => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Spatie Permission
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // Optional: give your new middleware an alias
            'sync.cart'          => SyncGuestCart::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();