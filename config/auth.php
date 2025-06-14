<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great starting point is the "web" guard which uses
    | session storage and the "users" provider.
    |
    | Laravel typically uses a "web" guard for storing user states in sessions,
    | while the "api" guard is for stateless authentication using tokens.
    | Here, we define multiple Sanctum guards for different user types.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'sanctum_tenant' => [
            'driver' => 'sanctum',
            'provider' => 'tenants',
        ],
        'sanctum_house_owner' => [ // Changed from sanctum_agent
            'driver' => 'sanctum',
            'provider' => 'house_owners', // Points to the new provider
        ],
        'sanctum_administrator' => [
            'driver' => 'sanctum',
            'provider' => 'administrators',
        ],
        // You can keep a general 'api' guard if needed for other token-based authentication,
        // but for specific types, the above sanctum guards are more explicit.
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users', // Default Laravel user provider, can be adapted or removed if not used.
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms to authenticate a user.
    |
    | If you have multiple user tables or models, you may configure multiple
    | sources to retrieve the user's authenticatable model.
    |
    */

    'providers' => [
        'users' => [ // Default Laravel User provider
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // Keep this if you still have a general 'User' model
        ],
        'tenants' => [
            'driver' => 'eloquent',
            'model' => App\Models\Tenant::class,
        ],
        'house_owners' => [ // Changed from agents
            'driver' => 'eloquent',
            'model' => App\Models\HouseOwner::class, // Points to the new model
        ],
        'administrators' => [
            'driver' => 'eloquent',
            'model' => App\Models\Administrator::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify how the password reset tokens are stored in your application.
    | This is for the traditional web-based password resets, not typically
    | used directly with API tokens from Sanctum.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'tenants' => [ // You can add password reset for tenants
            'provider' => 'tenants',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'house_owners' => [ // You can add password reset for house_owners
            'provider' => 'house_owners',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'administrators' => [ // You can add password reset for administrators
            'provider' => 'administrators',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];
