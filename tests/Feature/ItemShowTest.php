<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要情報が表示される()
    {
        $seller = User::factory()->create(['name' => '出品者']);
        $item = Item::factory()->for($seller)->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 12345,
            'description' => '説明文です',
            'condition' => '良好',
        ]);

        // 複数カテゴリ（中間テーブルがあれば attach）
        if (method_exists($item, 'categories')) {
            $c1 = Category::factory()->create(['name' => 'トップス']);
            $c2 = Category::factory()->create(['name' => 'メンズ']);
            $item->categories()->attach([$c1->id, $c2->id]);
        }

        $response = $this->get(route('items.show', $item))
            ->assertSee('テスト商品')
            ->assertSee('テストブランド')
            ->assertSee('12345')
            ->assertSee('説明文です')
            ->assertSee('良好');

        if (method_exists($item, 'categories')) {
            $response->assertSee('トップス')->assertSee('メンズ');
        }
    }
}
