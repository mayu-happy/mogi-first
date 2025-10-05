<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Item;


class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = Item::find(29); // 任意の商品ID

        $comments = [
            [
                'item_id' => $item->id,
                'user_id' => 1,
                'body' => 'この商品、色違いもありますか？',
            ],
            [
                'item_id' => $item->id,
                'user_id' => 1,
                'body' => '配送はどれくらいかかりますか？',
            ],
            [
                'item_id' => $item->id,
                'user_id' => 1,
                'body' => '状態は写真通りですか？',
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}
