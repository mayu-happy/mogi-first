<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Http\Responses\LoginToHome;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ログイン後 → /
        $this->app->singleton(LoginResponse::class, LoginToHome::class);

        // （保険）登録後 → /
        $this->app->singleton(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect()->to('/');
            }
        });
    }
    public function boot(): void
    {
        // ログイン後 → / に強制
        $this->app->extend(LoginResponse::class, function ($service, $app) {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect()->to('/'); // 必要なら '/top' 等に
                }
            };
        });

        // 登録後 → /（保険でこちらも）
        $this->app->extend(RegisterResponse::class, function ($service, $app) {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->to('/');
                }
            };
        });
    }
}
