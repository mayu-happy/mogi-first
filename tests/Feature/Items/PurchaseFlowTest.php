<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_purchase_item_and_is_redirected()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->for($seller)->create([
            'name'  => '購入テスト商品',
            'price' => 1234,
        ]);

        // 購入実行
        $res = $this->actingAs($buyer)
            ->post(route('purchase.store', $item));

        // 購入後の遷移先（商品一覧に戻す仕様）
        $res->assertRedirect(route('items.index'));

        // 購入レコードが作成されている
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'price'   => $item->price,
        ]);
    }

    /** @test */
    public function purchased_item_shows_sold_label_on_index()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->for($seller)->create(['name' => 'SOLD表示テスト']);

        // 事前に購入済みにする
        Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'price'   => $item->price,
        ]);

        // 商品一覧に SOLD が出ること
        $res = $this->get(route('items.index'));
        $res->assertOk()
            ->assertSee('SOLD'); // 一覧の売り切れオーバーレイの文言
    }

    /** @test */
    public function purchased_item_appears_in_profile_buy_tab()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->for($seller)->create(['name' => 'プロフィール購入品']);

        // 購入済み
        Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'price'   => $item->price,
        ]);

        // マイページの「購入した商品」タブに表示される
        // ルートは /mypage/buy -> /mypage/profile?tab=buy にリダイレクト
        $res = $this->actingAs($buyer)->get(route('mypage.buy'));
        $res->assertRedirect(route('mypage.profile', ['tab' => 'buy']));

        $res2 = $this->actingAs($buyer)->get(route('mypage.profile', ['tab' => 'buy']));
        $res2->assertOk()
            ->assertSee('プロフィール購入品'); // 購入した商品の名称が出ている
    }
}
