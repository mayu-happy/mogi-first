<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsIndexTest extends TestCase
{
    use RefreshDatabase;

    /** ゲスト：全商品が見えて、購入済みに SOLD が付く */
    public function test_guest_sees_all_items_and_sold_label_is_shown_for_purchased_items()
    {
        $sellerA = User::factory()->create();
        $sellerB = User::factory()->create();
        $sellerC = User::factory()->create();

        $unsoldA = Item::factory()->for($sellerA)->create(['name' => '未購入A']);
        $unsoldB = Item::factory()->for($sellerB)->create(['name' => '未購入B']);
        $sold    = Item::factory()->for($sellerC)->create(['name' => '購入済みC']);

        // 購入レコード（購入者は誰でもOK）
        $buyer = User::factory()->create();
        Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $sold->id,
            'price'   => $sold->price,
        ]);

        $res = $this->get(route('items.index'));

        $res->assertOk()
            ->assertSee('未購入A')
            ->assertSee('未購入B')
            ->assertSee('購入済みC')
            ->assertSee('SOLD'); // ビューの表記に合わせて必要なら変更
    }

    /** ログイン時：自分の出品は一覧に出ない */
    public function test_authenticated_user_does_not_see_own_items()
    {
        $me = User::factory()->create();
        $otherSeller = User::factory()->create();

        $mine   = Item::factory()->for($me)->create(['name' => '自分の出品']);
        $others = Item::factory()->for($otherSeller)->create(['name' => '他人の出品']);

        $res = $this->actingAs($me)->get(route('items.index'));

        $res->assertOk()
            ->assertDontSee('自分の出品')
            ->assertSee('他人の出品');
    }

    /** @test ログイン時でも購入済み商品には SOLD が付く */
    public function test_authenticated_user_sees_sold_label_on_purchased_items()
    {
        $viewer = \App\Models\User::factory()->create();
        $seller = \App\Models\User::factory()->create();

        $item = \App\Models\Item::factory()->for($seller)->create(['name' => '売切れ商品']);
        $buyer = \App\Models\User::factory()->create();

        \App\Models\Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,   // ← $sold ではなく $item
            'price'   => $item->price,
        ]);

        $res = $this->actingAs($viewer)->get(route('items.index'));

        $res->assertOk()
            ->assertSee('売切れ商品')
            ->assertSee('SOLD');
    }
}
