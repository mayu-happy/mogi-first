<?php

return [
    'required' => ':attribute を入力してください。',
    'email' => '正しいメールアドレス形式で入力してください。',
    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください。',
    ],
    'confirmed' => ':attribute と一致しません。',
    'unique' => 'その :attribute はすでに使用されています。',

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => '確認用パスワード',
    ],
];
