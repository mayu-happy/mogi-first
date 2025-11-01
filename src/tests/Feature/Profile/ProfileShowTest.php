<?php

namespace Tests\Feature\Profile;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_shows_avatar_name_sell_and_buy_lists()
    {
        $me    = User::factory()->create(['name' => 'テスト太郎', 'image' => 'profile_images/dummy.png']);
        $sell1 = Item::factory()->for($me)->create(['name' => '出品A']);
        $sell2 = Item::factory()->for($me)->create(['name' => '出品B']);

        $other = User::factory()->create();
        $buyItem = Item::factory()->for($other)->create(['name' => '購入したアイテム']);
        Purchase::factory()->create([
            'user_id' => $me->id,
            'item_id' => $buyItem->id,
            'price'   => $buyItem->price,
        ]);

        $this->actingAs($me)
            ->get(route('mypage.profile'))
            ->assertOk()
            // アバターimg（プレースホルダ含め常に <img src="{{ $user->avatar_url }}"> 仕様）
            ->assertSee('img', false)
            ->assertSee('テスト太郎')
            // 出品リスト
            ->assertSee('出品A')
            ->assertSee('出品B')
            // 購入リスト（タブ切替は別としてHTMLに出力される名称を確認）
            ->assertSee('購入した商品')
            ->assertSee('購入したアイテム');
    }
}
