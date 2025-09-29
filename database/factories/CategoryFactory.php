<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(), // テーブルに name がある想定
            // 'slug' => $this->faker->unique()->slug(), // カラムがあれば有効化
        ];
    }
}
