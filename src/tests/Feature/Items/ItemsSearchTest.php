<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsSearchTest extends TestCase
{
    use RefreshDatabase;

    /** 部分一致で「商品名」にヒットしたものだけが表示される（説明は除外でOKなら名前のみ検証） */
    public function test_name_partial_match_search_shows_matching_items_only()
    {
        $seller = User::factory()->create();

        $hit1 = Item::factory()->for($seller)->create(['name' => 'コーヒーミル 木製']);
        $hit2 = Item::factory()->for($seller)->create(['name' => '手挽きコーヒーグラインダー']);
        $miss = Item::factory()->for($seller)->create(['name' => '紅茶ポット']);

        // recommend タブで検索
        $res = $this->get(route('items.index', ['tab' => 'recommend', 'q' => 'コーヒー']));

        $res->assertOk()
            ->assertSee('コーヒーミル 木製')
            ->assertSee('手挽きコーヒーグラインダー')
            ->assertDontSee('紅茶ポット');

        // 検索欄の value にキーワードが保持されている
        $res->assertSee('name="q" value="コーヒー"', false);
    }

    /** 検索状態（q）がマイリストでも保持される：リンク維持＆遷移後も value に残る */
    public function test_search_state_is_preserved_on_mylist_tab()
    {
        $me = User::factory()->create();
        $seller = User::factory()->create();

        $likedMatch   = Item::factory()->for($seller)->create(['name' => 'コーヒーマグ']);
        $likedNoMatch = Item::factory()->for($seller)->create(['name' => '紅茶カップ']);

        // マイリストに2件入れる（このうち1件だけが検索にヒット）
        $likedMatch->likedBy()->attach($me->id);
        $likedNoMatch->likedBy()->attach($me->id);

        // まず recommend タブで検索して、タブリンクに q が引き継がれていることを確認
        $res = $this->actingAs($me)->get(route('items.index', ['tab' => 'recommend', 'q' => 'コーヒー']));
        $res->assertOk();

        // マイリストタブのリンクに q= が含まれる（URLエンコードされる想定）
        $res->assertSee('items?tab=mylist&amp;q=' . rawurlencode('コーヒー'), false);

        // 実際に mylist へ遷移して、検索 value と絞り込みが維持されること
        $res2 = $this->actingAs($me)->get(route('items.index', ['tab' => 'mylist', 'q' => 'コーヒー']));
        $res2->assertOk()
            // 検索欄の value が保持
            ->assertSee('name="q" value="コーヒー"', false)
            // ヒットする「いいね」商品は表示
            ->assertSee('コーヒーマグ')
            // ヒットしない「いいね」商品は表示されない
            ->assertDontSee('紅茶カップ');
    }
}
