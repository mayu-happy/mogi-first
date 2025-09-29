<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証はログインへリダイレクト()
    {
        $this->get(route('mypage.likes'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $me = User::factory()->create();
        $liked = Item::factory()->create(['name' => 'いいね済み']);
        $not   = Item::factory()->create(['name' => '未いいね']);

        $me->likedItems()->attach($liked->id);

        $this->actingAs($me)->get(route('mypage.likes'))
            ->assertSee('いいね済み')
            ->assertDontSee('未いいね');
    }

    /** @test */
    public function 購入済み商品には_SOLD_が表示される()
    {
        $me = User::factory()->create();
        $item = Item::factory()->create(['name' => '購入済み']);

        $me->likedItems()->attach($item->id);
        Purchase::factory()->create(['user_id' => $me->id, 'item_id' => $item->id]);

        $this->actingAs($me)->get(route('mypage.likes'))
            ->assertSee('SOLD'); // 実際の文言に合わせて調整
    }
}
