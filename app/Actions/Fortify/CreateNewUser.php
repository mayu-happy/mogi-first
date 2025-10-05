<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;   // ★ 追加

class CreateNewUser implements CreatesNewUsers   // ★ implements を追加
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input)
    {
        Validator::make(
            $input,
            [
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(8)],
            ],
            // 任意メッセージ（必要に応じて）
            [
                'password.confirmed' => 'パスワードが一致しません。',
                'password.required'  => 'パスワードを入力してください。',
                'password.min'       => 'パスワードは8文字以上で入力してください。',
                'email.email'        => 'メールアドレスの形式が正しくありません。',
                'name.required'      => 'お名前を入力してください。',
            ],
            [
                'password' => 'パスワード',
                'email'    => 'メールアドレス',
                'name'     => 'お名前',
            ]
        )->validate();

        return User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
