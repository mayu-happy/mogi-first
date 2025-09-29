<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインは購入できない()
    {
        $item = Item::factory()->create();

        $this->post(route('purchase.store', $item), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('login'));
    }

    /** @test */
    public function 正常購入でレコード作成_一覧はSOLD_購入履歴に表示()
    {
        // 購入者（配送先が必要）
        $buyer = User::factory()->create([
            'postal_code' => '123-4567',
            'address'     => '東京都千代田区1-1-1',
            'building'    => 'テストビル101',
        ]);

        // 出品商品
        $item = Item::factory()->create([
            'name'  => '購入対象',
            'price' => 12345,
        ]);

        // ログインして購入
        $this->actingAs($buyer)
            ->post(route('purchase.store', $item), [
                'payment_method' => 'カード支払い', // コントローラの検証に合わせる
            ])
            ->assertRedirect(route('mypage.buy'))
            ->assertSessionHasNoErrors();

        // 1) 購入レコードができている
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status'  => 'paid',
        ]);

        // 2) 商品一覧に SOLD 表示（UIの表記に合わせて微調整可）
        $this->get(route('items.index'))
            ->assertOk()
            ->assertSee('SOLD');

        // 3) マイページの購入一覧に含まれる
        $this->get(route('mypage.buy'))
            ->assertOk()
            ->assertSee('購入対象');
    }
}
