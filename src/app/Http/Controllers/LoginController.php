<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request)
    {
        $cred = $request->validated();

        if (Auth::attempt(
            ['email' => $cred['email'], 'password' => $cred['password']],
            (bool)($cred['remember'] ?? false)
        )) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'メールアドレスまたはパスワードが違います。'])
            ->onlyInput('email');
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
