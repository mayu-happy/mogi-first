<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function submit(array $overrides = [])
    {
        $valid = [
            'name' => '太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        return $this->from(route('register')) // ← ルート名は実装に合わせて
            ->post(route('register'), array_merge($valid, $overrides));
    }

    /** @test */ public function 名前が空だとエラー()
    {
        $this->submit(['name' => ''])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['name']);
    }

    /** @test */ public function メールが空だとエラー()
    {
        $this->submit(['email' => ''])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email']);
    }

    /** @test */ public function パスワードが空だとエラー()
    {
        $this->submit(['password' => '', 'password_confirmation' => ''])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password']);
    }

    /** @test */ public function パスワード7文字以下でエラー()
    {
        $this->submit(['password' => '1234567', 'password_confirmation' => '1234567'])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password']);
    }

    /** @test */ public function 確認用と不一致でエラー()
    {
        $this->submit(['password_confirmation' => 'different'])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password']);
    }

    /** @test */ public function 正常登録でプロフィール設定へ遷移()
    {
        $this->submit()
            ->assertRedirect(route('mypage.profile')); // ← 遷移先に合わせて
        $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);
    }
}
