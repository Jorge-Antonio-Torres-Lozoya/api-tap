<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
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
    }
}
