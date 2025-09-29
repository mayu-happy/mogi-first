<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインは購入できない()
    {
        $item = Item::factory()->create();
        $this->post(route('purchase.updatePayment', $item), ['payment_method' => 'card'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function 正常購入でレコード作成_SOLD表示へ反映()
    {
        $buyer = User::factory()->create();
        $item  = Item::factory()->create(['name' => '購入テスト商品']);

        $this->actingAs($buyer)
            ->post(route('purchase.updatePayment', $item), [
                'payment_method' => 'カード支払い',
                // 住所系の必須があればここに追加：
                // 'postal_code' => '1234567', 'address' => '東京都...', 'building' => 'A-101'
            ])
            ->assertStatus(302);

        // 購入レコード作成
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        // 一覧に SOLD（表記に合わせて変更可）
        $this->get(route('items.index'))->assertSee('SOLD');
    }
}
