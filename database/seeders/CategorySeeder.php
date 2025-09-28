<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ'
        ];

        foreach ($categories as $i => $name) {
            \App\Models\Category::updateOrCreate(['id' => $i + 1],  ['name' => $name, 'sort_order' => $i + 1]);
        }
    }
}
