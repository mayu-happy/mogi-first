<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Item, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemSearchTest extends TestCase
{
use RefreshDatabase;

/** @test */
public function 商品名部分一致でヒットする()
{
Item::factory()->create(['name' => 'Air Max 90']);
Item::factory()->create(['name' => 'Jordan 1']);

$this->get(route('items.index', ['keyword' => 'Air']))
->assertSee('Air Max 90')
->assertDontSee('Jordan 1');
}

/** @test */
public function マイリストに遷移しても検索キーワードが保持される()
{
$user = User::factory()->create();
$this->actingAs($user)
->get(route('items.index', ['keyword' => 'Air']))
->assertSee('Air');

// セッションやクエリ保持の実装に合わせて
$this->get(route('mypage.likes', ['keyword' => 'Air']))
->assertSee('Air');
}
}