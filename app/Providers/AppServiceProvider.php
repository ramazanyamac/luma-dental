<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\DevCommands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        DevCommands::artisan('queue:listen --tries=3 --timeout=30', 'queue');

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())->response(
                fn ($request, $headers) => response()->json(['message' => __('contact.form.rate_limited', [], $request->is('tr/*') ? 'tr' : 'en')], 429, $headers)
            );
        });
    }
}
