<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            // ★ 出品者をデフォルトで紐づけ（user_id NOT NULL 対策）
            'user_id'    => User::factory(),
            'name'       => $this->faker->words(2, true),
            'description' => $this->faker->paragraph(),
            'price'      => $this->faker->numberBetween(500, 20000),
            'condition'  => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'brand'      => $this->faker->company(),
            'img_url'    => null, // 画像なし既定でOK（noimage.svg 対応）
        ];
    }
}
