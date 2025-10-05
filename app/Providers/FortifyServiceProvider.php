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
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 登録完了後のリダイレクト先を強制
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('mypage.profile.edit');
                }
            };
        });
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ▼ ビュー割り当て
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // ▼ アクション割り当て
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ▼ レート制限
        RateLimiter::for('login', function (Request $request) {
            $key = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($key);
        });
        RateLimiter::for('two-factor', fn(Request $r) => Limit::perMinute(5)->by($r->session()->get('login.id')));

        // ▼ 登録完了後のリダイレクト（テスト期待どおり）
        \Laravel\Fortify\Fortify::redirects('register', '/mypage/profile/edit');        // もしくは文字列でもOK: Fortify::redirects('register', '/mypage/profile/edit');
    }
}
