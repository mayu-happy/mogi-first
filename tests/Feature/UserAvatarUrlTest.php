<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserAvatarUrlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 画像が無ければデフォルトを返す()
    {
        $u = User::factory()->create(['image' => null]);
        $this->assertStringContainsString('/images/avatar-default.png', $u->avatar_url);
    }

    /** @test */
    public function publicディスクの画像があればstorage配下URLを返す()
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile_images/a.png', 'x');

        $u = User::factory()->create(['image' => 'profile_images/a.png']);
        $this->assertSame(Storage::url('profile_images/a.png'), $u->avatar_url);
    }

    /** @test */
    public function storageや先頭スラッシュが付いていても正規化される()
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile_images/b.png', 'x');

        $u = User::factory()->create(['image' => 'storage/profile_images/b.png']);
        $this->assertSame('/storage/profile_images/b.png', $u->avatar_url);
    }

    /** @test */
    public function 外部URLはそのまま返す()
    {
        $u = User::factory()->create(['image' => 'https://example.com/x.png']);
        $this->assertSame('https://example.com/x.png', $u->avatar_url);
    }

    /** @test */
    public function 実ファイルが無ければデフォルトにフォールバックする()
    {
        $u = User::factory()->create(['image' => 'profile_images/not-exist.png']);
        $this->assertStringContainsString('/images/avatar-default.png', $u->avatar_url);
    }
}
