<?php

return [
    // よく使うルール
    'required' => ':attributeを入力してください。',
    'email'    => ':attributeの形式が正しくありません。',
    'max'      => ['string' => ':attributeは:max文字以内で入力してください。'],
    'min'      => ['string' => ':attributeは:min文字以上で入力してください。'],
    'confirmed' => ':attributeが確認用と一致しません。',
    'unique'   => 'その:attributeは既に使用されています。',

    // このプロジェクト固有の上書き（例：コメント本文）
    'custom' => [
        'name' => [
            'required' => 'お名前を入力してください',
        ],
        'email' => [
            'required' => 'メールアドレスを入力してください',
        ],
        'password' => [
            'required'  => 'パスワードを入力してください',
            'min'       => 'パスワードは8文字以上で入力してください',
            'confirmed' => 'パスワードと一致しません',
        ],
    ],

    // :attribute の表示名
    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'name' => 'お名前',
        'body' => 'コメント',
        'q'    => 'キーワード',
    ],
];
