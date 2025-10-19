<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_store_item_with_all_required_fields_and_image()
    {
        $user = User::factory()->create();
        $cat1 = Category::factory()->create(['name' => 'アクセサリー']);
        $cat2 = Category::factory()->create(['name' => 'ピアス']);

        Storage::fake('public');
        $img = UploadedFile::fake()->image('pias.png', 600, 600);

        $payload = [
            'name'        => 'ピアスA',
            'brand'       => 'NO BRAND',
            'description' => 'きらきらのピアス',
            'condition'   => '良好',
            'price'       => 3200,
            'categories'  => [$cat1->id, $cat2->id],
            'image'       => $img,
        ];

        $this->actingAs($user)
            ->post(route('sell.store'), $payload)
            ->assertRedirect(); // 成功後どこかへ

        // 画像が保存されている（items/xxxx）
        Storage::disk('public')->assertExists('items');

        // DB（items と 中間テーブル）
        $this->assertDatabaseHas('items', [
            'name'      => 'ピアスA',
            'brand'     => 'NO BRAND',
            'condition' => '良好',
            'price'     => 3200,
            'user_id'   => $user->id,
        ]);

        $itemId = \App\Models\Item::where('name', 'ピアスA')->value('id');

        $this->assertDatabaseHas('category_item', [
            'item_id'     => $itemId,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $itemId,
            'category_id' => $cat2->id,
        ]);
    }
}
