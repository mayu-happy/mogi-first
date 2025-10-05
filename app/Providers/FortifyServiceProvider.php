<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ▼ ビュー割り当て
        \Laravel\Fortify\Fortify::loginView(fn() => view('auth.login'));
        \Laravel\Fortify\Fortify::registerView(fn() => view('auth.register'));

        // ▼ アクションの割り当て
        \Laravel\Fortify\Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUser::class);
        \Laravel\Fortify\Fortify::updateUserProfileInformationUsing(\App\Actions\Fortify\UpdateUserProfileInformation::class);
        \Laravel\Fortify\Fortify::updateUserPasswordsUsing(\App\Actions\Fortify\UpdateUserPassword::class);
        \Laravel\Fortify\Fortify::resetUserPasswordsUsing(\App\Actions\Fortify\ResetUserPassword::class);
        
        // ▼ RateLimiter
        RateLimiter::for('login', function (Request $request) {
            $key = Str::transliterate(Str::lower($request->input(\Laravel\Fortify\Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('two-factor', fn(Request $r) => Limit::perMinute(5)->by($r->session()->get('login.id')));
    }
}
