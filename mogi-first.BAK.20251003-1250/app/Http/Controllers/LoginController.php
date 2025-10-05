<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;


class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if (auth()->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ])->withInput();
    }
}
