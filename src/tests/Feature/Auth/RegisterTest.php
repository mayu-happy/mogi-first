<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力ならバリデーションメッセージが表示される()
    {
        $res = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '',
                'email' => 'u@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $res->assertRedirect(route('register'));
        $res->assertSessionHasErrors(['name']);

        $this->followingRedirects()
            ->get(route('register'))
            ->assertSee('お名前を入力してください');
    }

    /** @test */
    public function メールアドレスが未入力ならバリデーションメッセージが表示される()
    {
        $res = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '太郎',
                'email' => '',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $res->assertRedirect(route('register'));
        $res->assertSessionHasErrors(['email']);

        $this->followingRedirects()
            ->get(route('register'))
            ->assertSee('メールアドレスを入力してください');
    }

    /** @test */
    public function パスワードが未入力ならバリデーションメッセージが表示される()
    {
        $res = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '太郎',
                'email' => 'u@example.com',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $res->assertRedirect(route('register'));
        $res->assertSessionHasErrors(['password']);

        $this->followingRedirects()
            ->get(route('register'))
            ->assertSee('パスワードを入力してください');
    }

    /** @test */
    public function パスワードが7文字以下ならバリデーションメッセージが表示される()
    {
        $res = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '太郎',
                'email' => 'u@example.com',
                'password' => '1234567',
                'password_confirmation' => '1234567',
            ]);

        $res->assertRedirect(route('register'));
        $res->assertSessionHasErrors(['password']);

        $this->followingRedirects()
            ->get(route('register'))
            ->assertSee('パスワードは8文字以上で入力してください');
    }

    /** @test */
    public function 確認用パスワードと一致しない場合はバリデーションメッセージが表示される()
    {
        $res = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '太郎',
                'email' => 'u@example.com',
                'password' => 'password123',
                'password_confirmation' => 'mismatch',
            ]);

        $res->assertRedirect(route('register'));
        $res->assertSessionHasErrors(['password']);

        $this->followingRedirects()
            ->get(route('register'))
            ->assertSee('パスワードと一致しません');
    }

    /** @test */
    public function 全ての項目が正しければ登録されプロフィール設定に遷移する()
    {
        $res = $this->post(route('register'), [
            'name' => '太郎',
            'email' => 'u@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'u@example.com']);

        // 期待する遷移先（プロフィール編集）に合わせて検証
        $res->assertRedirect(route('mypage.profile.edit'));

        $this->followingRedirects()
            ->get(route('mypage.profile.edit'))
            ->assertSee('プロフィール設定'); // 画面の見出し文言に合わせて
    }
}
