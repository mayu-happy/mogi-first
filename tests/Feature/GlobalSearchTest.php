<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名の部分一致でヒットする()
    {
        Item::factory()->create(['name' => 'ナイキ スニーカー']);
        Item::factory()->create(['name' => 'アディダス パーカー']);

        $this->get(route('items.index', ['keyword' => 'スニ']))
            ->assertSee('ナイキ スニーカー')
            ->assertDontSee('アディダス パーカー');
    }

    /** @test */
    public function 検索キーワードがマイリストでも保持される()
    {
        $this->withSession(['search.q' => 'バッグ']); // 実装に合わせてセッションキー変更可
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user)
            ->get(route('mypage.likes'))
            ->assertSee('バッグ');
    }
}
