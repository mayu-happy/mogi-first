<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** 認証時：いいねした商品のみ表示される（未いいねは表示されない） */
    public function test_authenticated_user_sees_only_liked_items()
    {
        $me = User::factory()->create();
        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();

        $liked    = Item::factory()->for($seller1)->create(['name' => 'いいね商品A']);
        $notLiked = Item::factory()->for($seller2)->create(['name' => '未いいね商品B']);

        // いいね付与（items.likedBy を想定）
        $liked->likedBy()->attach($me->id);

        $res = $this->actingAs($me)->get(route('items.index', ['tab' => 'mylist']));

        $res->assertOk()
            ->assertSee('いいね商品A')
            ->assertDontSee('未いいね商品B'); // 未いいねは出ない
    }

    /** 認証時：マイリスト内の購入済み商品には SOLD ラベルが表示される */
    public function test_authenticated_user_sees_sold_label_for_purchased_items_in_mylist()
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        $likedSold = Item::factory()->for($seller)->create(['name' => '売切れ（いいね）']);
        $likedSold->likedBy()->attach($me->id);

        // 別ユーザーが購入 → SOLD 扱い
        $buyer = User::factory()->create();
        Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $likedSold->id,
            'price'   => $likedSold->price,
        ]);

        $res = $this->actingAs($me)->get(route('items.index', ['tab' => 'mylist']));

        $res->assertOk()
            ->assertSee('売切れ（いいね）')
            ->assertSee('SOLD'); // 表示文言に合わせて必要なら変更
    }

    /** 未認証：マイリストは何も表示されない（空＝テキストも出さない） */
    public function test_guest_sees_empty_on_mylist()
    {
        // データがあっても、未認証の mylist は空表示
        $seller = User::factory()->create();
        $item = Item::factory()->for($seller)->create(['name' => 'データがあっても出ない']);

        $res = $this->get(route('items.index', ['tab' => 'mylist']));

        $res->assertOk()
            // アイテム名は出ない（空表示）
            ->assertDontSee('データがあっても出ない')
            // 一覧グリッド自体も出さない想定なら、クラスを見ておく（任意）
            ->assertDontSee('class="grid"');
    }
}
