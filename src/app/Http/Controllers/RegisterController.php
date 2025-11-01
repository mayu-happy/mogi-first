<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function submit(RegisterRequest $request)
    {
        $data = $request->validate(
            [
                'name'                  => ['required', 'string', 'max:255'],
                'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password'              => ['required', 'string', 'min:8'],      
                'password_confirmation' => ['required', 'same:password'],       
            ],
            [
                'password_confirmation.same'     => 'パスワードと一致しません。',
                'password_confirmation.required' => 'パスワードを入力してください。',
            ],
            [
                'name'                  => 'ユーザー名',
                'email'                 => 'メールアドレス',
                'password'              => 'パスワード',
                'password_confirmation' => '確認用パスワード',
            ]
        );

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
