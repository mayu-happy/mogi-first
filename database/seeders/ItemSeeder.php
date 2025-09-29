<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'condition' => '良好', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg', 'user_id' => 1],
            ['name' => 'HDD', 'price' => 5000, 'brand' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'condition' => '目立った傷や汚れなし', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg', 'user_id' => 2],
            ['name' => '玉ねぎ３束', 'price' => 300, 'brand' => 'なし', 'description' => '新鮮な玉ねぎ３束のセット', 'condition' => 'やや傷や汚れあり', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg', 'user_id' => 3],
            ['name' => '革靴', 'price' => 4000, 'brand' => '', 'description' => 'クラシックなデザインの革靴', 'condition' => '状態が悪い', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg', 'user_id' => 4],
            ['name' => 'ノートPC', 'price' => 45000, 'brand' => '', 'description' => '高性能なノートパソコン', 'condition' => '良好', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg', 'user_id' => 5],
            ['name' => 'マイク', 'price' => 8000, 'brand' => 'なし', 'description' => '高音質のレコーディング用マイク', 'condition' => '目立った傷や汚れなし', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg', 'user_id' => 6],
            ['name' => 'ショルダーバッグ', 'price' => 3500, 'brand' => '', 'description' => 'おしゃれなショルダーバッグ', 'condition' => 'やや傷や汚れあり', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg', 'user_id' => 7],
            ['name' => 'タンブラー', 'price' => 500, 'brand' => 'なし', 'description' => '使いやすいタンブラー', 'condition' => '状態が悪い', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg', 'user_id' => 8],
            ['name' => 'コーヒーミル', 'price' => 4000, 'brand' => 'Starbacks', 'description' => '手動のコーヒーミル', 'condition' => '良好', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg', 'user_id' => 9],
            ['name' => 'メイクセット', 'price' => 2500, 'brand' => '', 'description' => '便利なメイクアップセット', 'condition' => '目立った傷や汚れなし', 'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg', 'user_id' => 10],
        ];

        // 必要ユーザーを事前作成（存在すればスキップ）
        $now = now();
        foreach (collect($items)->pluck('user_id')->unique()->filter() as $uid) {
            DB::table('users')->insertOrIgnore([
                'id' => $uid,
                'name' => "user{$uid}",
                'email' => "user{$uid}@example.com",
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $payload = [];
        foreach ($items as $it) {
            $payload[] = $it + ['created_at' => $now, 'updated_at' => $now];
        }
        DB::table('items')->insert($payload);
    }
}
