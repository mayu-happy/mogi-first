<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchasePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_selection_reflects_on_subtotal_page(): void
    {
        // 購入者 / 出品者
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();

        // 未購入の商品（他人の出品）
        $item = Item::factory()->for($seller)->create();

        // 1) まずは小計ページ（確認画面）を開く。初期値は 'conbini'
        $res1 = $this->actingAs($buyer)->get(route('purchase.create', $item));
        $res1->assertOk();
        // 小計側に表示している支払い方法ラベル（初期はコンビニ支払い）
        $res1->assertSee('コンビニ支払い');

        // 2) 支払い方法編集ページを開く（フォーム表示）
        $edit = $this->actingAs($buyer)->get(route('purchase.payment.edit', $item));
        $edit->assertOk()
            ->assertSee('コンビニ支払い')
            ->assertSee('カード支払い');

        // 3) カード支払いを選択して送信 → 303 で小計へ
        $upd = $this->actingAs($buyer)->put(
            route('purchase.payment.update', $item),
            ['payment' => 'card']
        );
        $upd->assertStatus(303);
        $upd->assertRedirect(route('purchase.create', $item));

        // 4) 反映確認：小計画面に「カード支払い」と出ていること
        $res2 = $this->actingAs($buyer)->get(route('purchase.create', $item));
        $res2->assertOk()
            ->assertSee('カード支払い');
        // ->assertDontSee('コンビニ支払い'); // 表示部ではカードのみになっている想定
    }

    public function test_invalid_key_falls_back_to_default(): void
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();

        // 不正キーを送っても既定値 'conbini' になる
        $upd = $this->actingAs($buyer)->put(
            route('purchase.payment.update', $item),
            ['payment' => 'hacker'] // in: から外れてバリデーションで弾かれるのが理想
        );

        // バリデーション違反なら 302 で戻る（validation エラー想定）
        $upd->assertStatus(302);

        // 既定値はコンビニ支払い（create 内でフォールバック済み）
        $res = $this->actingAs($buyer)->get(route('purchase.create', $item));
        $res->assertOk()->assertSee('コンビニ支払い');
    }
}
