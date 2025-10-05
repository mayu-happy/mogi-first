<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */ public function メール未入力でエラー()
    {
        $this->from(route('login'))
            ->post('/login', ['email' => '', 'password' => 'secret'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);
    }

    /** @test */ public function パスワード未入力でエラー()
    {
        $this->from(route('login'))
            ->post('/login', ['email' => 'a@b.c', 'password' => ''])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['password']);
    }

    /** @test */ public function 未登録情報ならエラーメッセージ()
    {
        $this->from(route('login'))
            ->post('/login', ['email' => 'none@example.com', 'password' => 'secret'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']); // FortifyServiceProviderの実装で email にメッセージを載せている
    }

    /** @test */ public function 正しい情報でログインできる()
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $this->from(route('login'))
            ->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertStatus(302); // リダイレクト先は環境で異なるのでここでは厳密に見ない

        $this->assertAuthenticatedAs($user); // ← 「ログイン処理が実行される」の本質チェック
    }
}

