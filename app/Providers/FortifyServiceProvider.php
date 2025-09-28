<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });


        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            $request->validate(
                [
                    'email'    => ['required', 'string', 'email'],
                    'password' => ['required', 'string'],
                ],
                [
                    'email.required'    => 'メールアドレスを入力してください',
                    'email.email'       => '正しいメールアドレス形式で入力してください。',
                    'password.required' => 'パスワードを入力してください',
                ]
            );

            $user = \App\Models\User::where('email', $request->email)->first();

            if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            return $user;
        });

        Fortify::registerView(fn() => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::verifyEmailView(fn() => view('auth.verify-email'));
    }
}
