<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_post_comment_and_count_increases()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(
            route('items.comments.store', $item),
            ['body' => 'とても良さそうですね！'] // ← フィールド名は body
        );

        $res->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body'    => 'とても良さそうですね！',
        ]);

        $this->assertEquals(1, $item->fresh()->comments()->count());
    }

    /** @test */
    public function guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $res = $this->post(
            route('items.comments.store', $item),
            ['body' => '未ログインで投稿']
        );

        $res->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'body'    => '未ログインで投稿',
        ]);
    }

    /** @test */
    public function empty_comment_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->from(route('items.show', $item))
            ->actingAs($user)
            ->post(route('items.comments.store', $item), ['body' => '']); // 空

        $res->assertRedirect(route('items.show', $item));
        $res->assertSessionHasErrors(['body']); // ← エラーキーも body

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function over_255_chars_comment_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $tooLong = str_repeat('あ', 256);

        $res = $this->from(route('items.show', $item))
            ->actingAs($user)
            ->post(route('items.comments.store', $item), ['body' => $tooLong]);

        $res->assertRedirect(route('items.show', $item));
        $res->assertSessionHasErrors(['body']); // ← 255超でエラー想定

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }
}
