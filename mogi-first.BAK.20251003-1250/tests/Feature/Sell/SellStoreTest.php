<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームから必要情報を保存できる()
    {
        $user = User::factory()->create();
        $c1 = Category::factory()->create(['name' => 'トップス']);
        $c2 = Category::factory()->create(['name' => 'メンズ']);

        $payload = [
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'description' => '説明文',
            'condition'   => '良好',           // enum/文字列どちらでもOK（実装に合わせる）
            'price'       => 12345,
            'category_ids' => [$c1->id, $c2->id],
        ];

        $this->actingAs($user)
            ->post(route('items.store'), $payload)   // ルート名は実装に合わせて変更OK
            ->assertRedirect();                      // 保存後にどこかへリダイレクト

        // items に保存
        $this->assertDatabaseHas('items', [
            'name'        => 'テスト商品',
            'brand'       => 'テストブランド',
            'description' => '説明文',
            'condition'   => '良好',
            'price'       => 12345,
            'user_id'     => $user->id,
        ]);

        // 中間テーブル(category_item)に保存（多対多の場合）
        $item = Item::where('name', 'テスト商品')->first();
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $item->id,
            'category_id' => $c1->id,
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $item->id,
            'category_id' => $c2->id,
        ]);
    }

    /** @test */
    public function 必須バリデーションが効く()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('sell.create'))            // 失敗時に戻る先
            ->post(route('items.store'), [])        // 何も送らない
            ->assertRedirect(route('sell.create'))
            ->assertSessionHasErrors([
                'name',
                'description',
                'condition',
                'price',
                'category_ids'
            ]);
    }
}
