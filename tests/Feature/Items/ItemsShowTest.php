<?php

namespace Tests\Feature\Items;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsShowTest extends TestCase
{
    use RefreshDatabase;

    /** 必要な情報（画像/名前/ブランド/価格/いいね数/コメント数/説明/商品の情報/コメント情報）が表示される */
    public function test_item_show_displays_all_required_information()
    {
        // 出品者 & ビューア（未ログインでもOKだが、ここではログインしておく）
        $seller = User::factory()->create();
        $viewer = User::factory()->create();

        // カテゴリ
        $catA = Category::factory()->create(['name' => 'アクセサリー']);
        $catB = Category::factory()->create(['name' => 'レディース']);

        // 商品（画像URLは https でセット：Storage 存在チェックを回避してそのまま表示させる）
        $item = Item::factory()
            ->for($seller)
            ->create([
                'name'        => 'きらきらピアス',
                'brand'       => 'COACH',
                'price'       => 12345,
                'condition'   => '目立った傷や汚れなし',
                'description' => 'どんなシーンにも合う上品なピアスです。',
                'img_url'     => 'https://example.com/pierce.jpg',
            ]);

        // カテゴリ付与（多対多）
        $item->categories()->sync([$catA->id, $catB->id]);

        // いいね（likedBy 多対多想定）
        $liker1 = User::factory()->create();
        $liker2 = User::factory()->create();
        $item->likedBy()->attach([$liker1->id, $liker2->id]); // → 2件

        // コメント
        $cUser1 = User::factory()->create(['name' => 'テスト次郎']);
        $cUser2 = User::factory()->create(['name' => 'テスト花子']);

        Comment::factory()->for($item)->for($cUser1, 'user')->create([
            'body' => '可愛いですね！',
        ]);
        Comment::factory()->for($item)->for($cUser2, 'user')->create([
            'body' => '発送はいつ頃になりますか？',
        ]);

        $res = $this->actingAs($viewer)->get(route('items.show', $item));

        $res->assertOk();

        // 商品基本情報
        $res->assertSee('きらきらピアス')
            ->assertSee('COACH')
            ->assertSee('（税込）');

        // 価格（カンマ区切り）
        $res->assertSee('¥' . number_format(12345));

        // 画像（https のまま表示される想定）
        $res->assertSee('https://example.com/pierce.jpg');

        // いいね数・コメント数（合計2コメント）
        $res->assertSee('2'); // liked や comments のカウント表示（ミニ統計の数値）

        // 商品説明
        $res->assertSee('どんなシーンにも合う上品なピアスです。');

        // 商品の情報（カテゴリ・状態）
        $res->assertSee('アクセサリー')
            ->assertSee('レディース')
            ->assertSee('目立った傷や汚れなし');

        // コメントの表示（ユーザー名 + 本文）
        $res->assertSee('テスト次郎')
            ->assertSee('可愛いですね！')
            ->assertSee('テスト花子')
            ->assertSee('発送はいつ頃になりますか？');
    }

    /** 複数カテゴリがチップ等で並んで表示される */
    public function test_multiple_categories_are_displayed()
    {
        $seller = User::factory()->create();

        $cat1 = Category::factory()->create(['name' => 'メンズ']);
        $cat2 = Category::factory()->create(['name' => '時計']);
        $cat3 = Category::factory()->create(['name' => 'アンティーク']);

        $item = Item::factory()
            ->for($seller)
            ->create([
                'name'      => 'クラシック時計',
                'img_url'   => 'https://example.com/watch.jpg',
                'condition' => '良好',
            ]);

        $item->categories()->sync([$cat1->id, $cat2->id, $cat3->id]);

        $res = $this->get(route('items.show', $item));

        $res->assertOk()
            ->assertSee('クラシック時計')
            ->assertSee('メンズ')
            ->assertSee('時計')
            ->assertSee('アンティーク');
    }
}
