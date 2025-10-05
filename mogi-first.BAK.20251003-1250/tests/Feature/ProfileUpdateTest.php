<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\User;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 画像付きでプロフィール更新でき_publicに保存される()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->put(route('profile.update'), [
            'name'  => '新しい名前',
            'image' => UploadedFile::fake()->image('me.png', 200, 200),
        ]);

        $res->assertRedirect(); // 更新後の遷移先に合わせてOK
        $user->refresh();

        $this->assertNotEmpty($user->image);
        $this->assertStringStartsWith('profile_images/', $user->image);
        Storage::disk('public')->assertExists($user->image);
    }

    /** @test */
    public function nameは必須()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->from(route('mypage.profile'))
            ->put(route('profile.update'), ['name' => '']);

        $res->assertRedirect(route('mypage.profile'))
            ->assertSessionHasErrors(['name']);
    }
}
