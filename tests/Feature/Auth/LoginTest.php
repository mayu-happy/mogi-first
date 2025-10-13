<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** メールアドレス未入力でバリデーションエラー */
    public function test_email_is_required()
    {
        $response = $this->from(route('login'))->post('/login', [
            'email'    => '',
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'email' => 'メールアドレスを入力してください',
            ]);

        $this->assertGuest();
    }

    /** パスワード未入力でバリデーションエラー */
    public function test_password_is_required()
    {
        $response = $this->from(route('login'))->post('/login', [
            'email'    => 'user@example.com',
            'password' => '',
        ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'password' => 'パスワードを入力してください',
            ]);

        $this->assertGuest();
    }

    /** 未登録の情報でログインすると汎用エラー */
    public function test_invalid_credentials_show_generic_error()
    {
        $response = $this->from(route('login'))->post('/login', [
            'email'    => 'not-registered@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'email' => 'ログイン情報が登録されていません',
            ]);

        $this->assertGuest();
    }

    /** 正しい情報ならログインできる */
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'ok@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'ok@example.com',
            'password' => 'password123',
        ]);

        // Fortify のデフォルトは intended or '/'
        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));
    }
}
