<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function edited_address_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 住所編集画面→更新（セッション保存のみ）
        $this->actingAs($user)
            ->put(route('purchase.address.update', $item), [
                'postal_code' => '123-4567',
                'address'     => '東京都渋谷区1-2-3',
                'building'    => '渋谷ビル101',
            ])->assertRedirect(route('purchase.create', $item));

        // 小計に反映
        $this->actingAs($user)
            ->get(route('purchase.create', $item))
            ->assertOk()
            ->assertSee('123-4567')
            ->assertSee('東京都渋谷区1-2-3')
            ->assertSee('渋谷ビル101');
    }

    /** @test */
    public function purchased_item_is_saved_with_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // セッションに住所を入れる
        $this->actingAs($user)
            ->withSession(['purchase.address' => [
                'postal_code' => '234-5678',
                'address'     => '東京都世田谷区4-5-6',
                'building'    => '世田谷ビル202',
            ]])->post(route('purchase.store', $item))
            ->assertRedirect(route('items.index')); // 実装どおり一覧へ

        $this->assertDatabaseHas('purchases', [
            'user_id'     => $user->id,
            'item_id'     => $item->id,
            'postal_code' => '234-5678',
            'address'     => '東京都世田谷区4-5-6',
            'building'    => '世田谷ビル202',
        ]);
    }
}
