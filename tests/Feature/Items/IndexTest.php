<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Item, Purchase};

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品が表示される()
    {
        $items = Item::factory()->count(3)->create();
        $res = $this->get(route('items.index'));
        foreach ($items as $i) {
            $res->assertSee($i->name);
        }
    }

    /** @test */
    public function 購入済み商品はSOLDが表示される()
    {
        $buyer = User::factory()->create();
        $item  = Item::factory()->create(['name' => '購入済み']);
        Purchase::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id]);

        $this->get(route('items.index'))
            ->assertSee('SOLD'); // 画面の表記に合わせて
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $me    = User::factory()->create();
        $mine  = Item::factory()->for($me)->create(['name' => '自分の出品']);
        $other = Item::factory()->create(['name' => '他人の出品']);

        $this->actingAs($me)->get(route('items.index'))
            ->assertDontSee('自分の出品')
            ->assertSee('他人の出品');
    }
}
