<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        // まずは“候補”を作る（存在しない可能性のあるキーも含めてOK）
        $candidate = [
            'name'        => $this->faker->words(2, true),
            'brand'       => $this->faker->company(),
            'price'       => $this->faker->numberBetween(500, 50000),
            'description' => $this->faker->realText(50),
            'condition'   => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'user_id'     => User::factory(),
            // 画像カラムはプロジェクトにより異なるので候補を全部作る
            'image'       => 'images/sample.jpg',       // 例: 旧実装
            'img_url'     => 'images/sample.jpg',       // ★ あなたのテーブルはこれ
            'image_url'   => 'images/sample.jpg',       // 例: 別教材の命名
            // これらはテーブルに無ければ後で落とされる
            'status'      => '出品中',
            'category_id' => null,
        ];

        // 実際に items テーブルに存在するカラムだけを残す
        $columns = Schema::getColumnListing('items');          // ['brand','category_id','condition',...,'img_url',...]
        $allowed = array_flip($columns);
        return array_intersect_key($candidate, $allowed);
    }
}
