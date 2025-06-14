<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    $this->app->make(RateLimiter::class)->for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    // Route::middleware('api')
    //     ->prefix('api') // This line is crucial for the /api prefix
    //     ->group(base_path('routes/api.php'));

    // Route::middleware('web')
    //     ->group(base_path('routes/web.php'));
}
}
