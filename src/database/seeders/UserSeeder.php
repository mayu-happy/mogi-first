<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // サンプルユーザー1
        User::updateOrCreate(
            ['email' => 'sample1@example.com'],
            [
                'name'         => 'テスト一郎',
                'password'     => Hash::make('password'),
                'postal_code'  => '100-0001',
                'address'      => '東京都千代田区千代田1-1',
                'building'     => '千代田ビル１F',
                'image'        => 'profile_images/sample1.png',
            ]
        );

        // サンプルユーザー2
        User::updateOrCreate(
            ['email' => 'sample2@example.com'],
            [
                'name'        => 'テスト花子',
                'password'    => Hash::make('password'),
                'postal_code' => '202-0002',
                'address'     => '東京都港区芝公園2-2',
                'building'    => '港ビル２F',
                'image'       => 'profile_images/sample2.png',
            ]
        );
    }
}
