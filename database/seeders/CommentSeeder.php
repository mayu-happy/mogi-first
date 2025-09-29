<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'demo',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $target = Item::inRandomOrder()->first() ?? Item::create([
            'user_id' => $user->id,
            'name' => 'デモ商品',
            'price' => 1200,
            'brand' => 'SAMPLE',
            'description' => 'コメント用のサンプル商品',
            'condition' => '良好',
            'img_url' => 'https://placehold.co/600x600?text=Item',
        ]);

        foreach (
            [
                'この商品、色違いもありますか？',
                '発送はどれくらいで可能でしょうか？',
                '状態の写真をもう少し見たいです。',
            ] as $body
        ) {
            Comment::create([
                'item_id' => $target->id,
                'user_id' => $user->id,
                'body'    => $body,
            ]);
        }
    }
}
