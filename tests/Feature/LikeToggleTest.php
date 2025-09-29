<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインはいいねできずログインへリダイレクト()
    {
        $item = Item::factory()->create();
        $this->post(route('items.likes.toggle', $item))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function ログイン済みならトグルで追加と削除ができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 追加
        $this->post(route('items.likes.toggle', $item))->assertStatus(302);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'item_id' => $item->id]);

        // 解除
        $this->post(route('items.likes.toggle', $item))->assertStatus(302);
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
}
