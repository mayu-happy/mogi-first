<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** ログアウトできること */
    public function test_user_can_logout()
    {
        // 1) ログイン済みにする
        $user = User::factory()->create();
        $this->actingAs($user);

        // 念のためログイン状態を確認
        $this->assertAuthenticatedAs($user);

        // 2) ログアウト実行（Fortify は POST /logout）
        $response = $this->post('/logout');

        // 3) 未ログイン状態になっていること
        $this->assertGuest();

        // 4) リダイレクト先（デフォは / ＝ home）
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
    }
}
