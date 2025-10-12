<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->words(2, true),
            'description' => $this->faker->sentence(12),
            'price'       => $this->faker->numberBetween(500, 15000),
            'condition'   => $this->faker->randomElement(['良好', 'やや傷や汚れあり', '状態が悪い', '目立った傷や汚れなし']),
            'brand'       => $this->faker->optional()->company(),
            'img_url'     => null, // 画像なしダミーは null のまま
            // 'user_id'  => \App\Models\User::factory(), // ← ここは Seeder 側で上書きするので省略でもOK
        ];
    }
}
