<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_like_an_item_and_count_increases_and_icon_changes()
    {
        $me   = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($me);

        // 事前：未いいね
        $this->assertFalse($item->likedBy()->whereKey($me->id)->exists());

        // いいね
        $this->post(route('items.likes.toggle', $item))
            ->assertRedirect(); // 基本はリダイレクト返す

        // DB 反映
        $this->assertTrue($item->likedBy()->whereKey($me->id)->exists());

        // 確認は「商品詳細ページ」でのみ実施
        $res = $this->get(route('items.show', $item));
        $res->assertOk()
            // 押下状態
            ->assertSee('mini-stat is-liked', false)
            ->assertSee('aria-pressed="true"', false);

        // 件数 1 が出ていること（HTML断片で判定）
        $this->assertStringContainsString('mini-stat__num">1</span>', $res->getContent());
    }

    /** @test */
    public function user_can_unlike_an_item_and_count_decreases_and_icon_resets()
    {
        $me   = User::factory()->create();
        $item = Item::factory()->create();

        // 事前：いいね済みにしておく
        $item->likedBy()->attach($me->id);

        $this->actingAs($me);

        // 解除
        $this->post(route('items.likes.toggle', $item))
            ->assertRedirect();

        // DBから消えている
        $this->assertFalse($item->likedBy()->whereKey($me->id)->exists());

        // 確認は「商品詳細ページ」でのみ実施
        $res = $this->get(route('items.show', $item));
        $res->assertOk()
            ->assertDontSee('mini-stat is-liked', false)
            ->assertSee('aria-pressed="false"', false);

        // 件数 0
        $this->assertStringContainsString('mini-stat__num">0</span>', $res->getContent());
    }
}
