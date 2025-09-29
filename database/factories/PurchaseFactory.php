<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            // 住所や支払いなどカラムがあるなら適宜追加
            // 'postal_code' => '1234567',
            // 'address' => '東京都港区1-2-3',
            // 'building' => 'ABCビル',
            // 'payment_method' => 'card',
        ];
    }
}
