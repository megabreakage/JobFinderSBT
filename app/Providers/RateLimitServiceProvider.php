<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Authentication endpoints rate limiting (stricter)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(config('hrtalent.rate_limits.auth.attempts', 5))
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'code' => 'RATE_LIMIT_EXCEEDED',
                            'message' => 'Too many authentication attempts. Please try again later.',
                        ],
                    ], 429, $headers);
                });
        });

        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('hrtalent.rate_limits.api.attempts', 60))
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'code' => 'RATE_LIMIT_EXCEEDED',
                            'message' => 'Too many requests. Please slow down.',
                        ],
                    ], 429, $headers);
                });
        });

        // File upload rate limiting (more lenient)
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip());
        });

        // Search rate limiting
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
