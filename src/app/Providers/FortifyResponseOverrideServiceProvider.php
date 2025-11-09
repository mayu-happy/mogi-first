<?php

namespace App\Providers;

use App\Http\Responses\LoginToHome;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyResponseOverrideServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Fortify のログイン成功時レスポンスを差し替え
        $this->app->singleton(LoginResponse::class, LoginToHome::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
