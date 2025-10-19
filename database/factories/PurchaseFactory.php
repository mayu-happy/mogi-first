<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        // 出品者・商品・購入者を用意
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();

        return [
            'user_id' => User::factory(),   // buyer
            'item_id' => $item->id,
            'price'   => $item->price,
            // ← payment_method / postal_code / address / building は入れない
        ];
    }

    public function forItem(Item $item): self
    {
        return $this->state(fn() => [
            'item_id' => $item->id,
            'price'   => $item->price,
        ]);
    }

    public function by(User $buyer): self
    {
        return $this->state(fn() => [
            'user_id' => $buyer->id,
        ]);
    }
}
