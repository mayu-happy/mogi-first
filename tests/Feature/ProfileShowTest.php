<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要情報が表示される()
    {
        $u = User::factory()->create([
            'name' => '太郎',
            'postal_code' => '1234567',
            'address' => '東京都',
            'building' => 'ABC',
            'image' => 'profile_images/sample.png',
        ]);

        $sell = Item::factory()->for($u)->create(['name' => '出品商品']);
        $buyItem = Item::factory()->create(['name' => '購入商品']);
        Purchase::factory()->create(['user_id' => $u->id, 'item_id' => $buyItem->id]);

        $this->actingAs($u)->get(route('mypage.index')) // ルート名は合わせて
            ->assertSee('太郎')
            ->assertSee('出品商品')
            ->assertSee('購入商品');
    }

    /** @test */
    public function 変更画面で過去設定が初期値として出る()
    {
        $u = User::factory()->create([
            'name' => '花子',
            'postal_code' => '7654321',
            'address' => '大阪府',
            'building' => 'XYZ',
            'image' => 'profile_images/sample.png',
        ]);

        $this->actingAs($u)->get(route('profile.edit')) // ルート名は合わせて
            ->assertSee('花子')
            ->assertSee('7654321')
            ->assertSee('大阪府')
            ->assertSee('XYZ');
    }
}
