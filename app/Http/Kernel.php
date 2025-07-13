<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Handle CORS requests first to ensure they are processed correctly.
        // This is crucial for allowing your frontend (e.g., React) to communicate
        // with your backend API when they are on different domains/ports.
        \Illuminate\Http\Middleware\HandleCors::class,

        // Prevents requests from being executed during application maintenance mode.
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Limits the maximum size of uploaded files.
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trims whitespace from incoming request parameters.
        \App\Http\Middleware\TrimStrings::class,

        // Converts empty string request parameters to null.
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // Trust proxies for correct IP detection, especially if you're behind a load balancer.
        // Uncomment and configure if needed in App\Http\Middleware\TrustProxies.
        // \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Fruitcake\Cors\HandleCors::class, // Add this line if missing
],

    ];

    /**
     * The application's middleware aliases.
     *
     * These middleware may be assigned to groups or individual routes.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Custom authentication guards for your API (using Sanctum)
        'auth:sanctum_tenant' => \App\Http\Middleware\AuthenticateSanctumTenant::class,
        'auth:sanctum_house_owner' => \App\Http\Middleware\AuthenticateSanctumHouseOwner::class,
        'auth:sanctum_admin' => \App\Http\Middleware\AuthenticateSanctumAdmin::class,
    ];

    /**
     * The priority-sorted list of HTTP middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array<string>
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}