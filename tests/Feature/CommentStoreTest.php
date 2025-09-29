<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインはコメントできない()
    {
        $item = Item::factory()->create();
        $this->post(route('items.comments.store', $item), ['body' => 'hi'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function 本文が空ならバリデーション()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->from(route('items.show', $item))
            ->post(route('items.comments.store', $item), ['body' => ''])
            ->assertRedirect(route('items.show', $item))
            ->assertSessionHasErrors(['body']);
    }

    /** @test */
    public function 本文が255超でもバリデーション()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $long = str_repeat('あ', 256);

        $this->actingAs($user)->from(route('items.show', $item))
            ->post(route('items.comments.store', $item), ['body' => $long])
            ->assertRedirect(route('items.show', $item))
            ->assertSessionHasErrors(['body']);
    }

    /** @test */
    public function 正常ならコメント保存される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('items.comments.store', $item), ['body' => 'テストコメント'])
            ->assertRedirect(); // 詳細等へ

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body'    => 'テストコメント',
        ]);
    }
}
