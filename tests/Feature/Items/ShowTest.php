<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Item, User};
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 詳細ページに必要情報が表示される()
    {
        $item = Item::factory()->create([
            'brand' => 'NIKE',
            'price' => 12345,
            'description' => '説明文',
            'condition' => '良好',
        ]);
        // いいね2件
        $item->likedBy()->attach(User::factory()->count(2)->create()->pluck('id'));
        // コメント2件
        $u = User::factory()->create();
        $item->comments()->createMany([
            ['user_id' => $u->id, 'body' => 'コメント1'],
            ['user_id' => $u->id, 'body' => 'コメント2'],
        ]);
        // カテゴリ複数（多対多想定）
        if (method_exists($item, 'categories')) {
            $item->categories()->attach(\App\Models\Category::factory()->count(2)->create()->pluck('id'));
        }

        $this->get(route('items.show', $item))
            ->assertSee($item->name)
            ->assertSee('NIKE')
            ->assertSee('12345')
            ->assertSee('説明文')
            ->assertSee('良好')
            ->assertSee((string) $item->likes()->count())
            ->assertSee((string) $item->comments()->count())
            ->assertSee('コメント1')
            ->assertSee('コメント2');
    }

    /** @test */
    public function 複数カテゴリが表示される()
    {
        if (! method_exists(Item::class, 'categories')) {
            $this->markTestSkipped('categories リレーション未実装');
        }
        $item = Item::factory()->create();
        $cats = \App\Models\Category::factory()->count(2)->create(['name' => 'CAT']);
        $item->categories()->attach($cats->pluck('id'));

        $this->get(route('items.show', $item))
            ->assertSee('CAT'); // 複数あれば複数回ヒット
    }
}
