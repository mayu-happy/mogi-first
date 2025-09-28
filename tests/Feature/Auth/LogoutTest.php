<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */ public function ログアウトできる()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')        // Fortifyのデフォ（POST /logout）
            ->assertStatus(302);     // 遷移先は環境依存なので厳密に見ない

        $this->assertGuest();
    }
}
