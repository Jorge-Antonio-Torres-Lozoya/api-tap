<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

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
        // Sanctum reads its model from a static property, not from config — the
        // config key is ignored. Without this, Sanctum uses its default SQL model.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->configureRateLimiters();
    }

    /**
     * Define throttling rules for the authentication endpoints to protect
     * against brute-force, password spraying and password-reset abuse.
     */
    private function configureRateLimiters(): void
    {
        // Login: keyed by username + IP so one attacker cannot brute-force a
        // single account, while distributed attempts are still bounded per IP.
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(Str::lower((string) $request->input('username')) . '|' . $request->ip())
                ->response(fn() => $this->tooManyAttemptsResponse());
        });

        // Password recovery (forgot/reset): keyed by IP to limit email bombing
        // and reset-token guessing.
        RateLimiter::for('password', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(fn() => $this->tooManyAttemptsResponse());
        });
    }

    private function tooManyAttemptsResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Demasiados intentos. Inténtalo de nuevo en unos minutos.',
        ], 429);
    }
}
