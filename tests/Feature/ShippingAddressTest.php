<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 住所を更新すると購入画面に反映される()
    {
        $user = User::factory()->create([
            'postal_code' => '1000001',
            'address'     => '東京都千代田区',
            'building'    => '旧ビル101',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 1) 送付先住所を更新（住所変更画面の更新アクション）
        $payload = [
            'postal_code' => '1500001',
            'address'     => '東京都渋谷区神宮前1-1-1',
            'building'    => '新ビル502',
        ];
        $this->put(route('address.update'), $payload)->assertRedirect(); // 成功してどこかにリダイレクト

        // 2) 購入画面を再表示すると、新住所が反映されている
        $this->get(route('purchase.create', $item))
            ->assertSee('1500001')
            ->assertSee('東京都渋谷区神宮前1-1-1')
            ->assertSee('新ビル502');
    }

    /** @test */
    public function 購入時に購入レコードへ送付先住所が保存される()
    {
        $user = User::factory()->create([
            'postal_code' => '0600001',
            'address'     => '北海道札幌市中央区1-1-1',
            'building'    => 'タワー1203',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 念のため、住所を更新（既に上の値でもOK）
        $this->put(route('address.update'), [
            'postal_code' => '5300001',
            'address'     => '大阪府大阪市北区梅田1-1-1',
            'building'    => 'グラン梅田901',
        ])->assertRedirect();

        // 3) 購入実行（支払い方法はアプリの許可値に合わせる）
        $this->post(route('purchase.store', $item), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('mypage.buy'));

        // 4) purchases テーブルに送付先住所が保存されていること
        $this->assertDatabaseHas('purchases', [
            'user_id'               => $user->id,
            'item_id'               => $item->id,
            'shipping_postal_code'  => '5300001',
            'shipping_address'      => '大阪府大阪市北区梅田1-1-1',
            'shipping_building'     => 'グラン梅田901',
            'status'                => 'paid',
        ]);
    }
}
