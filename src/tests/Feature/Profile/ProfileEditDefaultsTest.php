<?php

namespace Tests\Feature\Profile;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileEditDefaultsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_edit_form_prefills_previous_values()
    {
        $me = User::factory()->create([
            'name'        => '既存ユーザー',
            'postal_code' => '111-2222',
            'address'     => '東京都千代田区1-1-1',
            'building'    => '皇居前101',
            'image'       => 'profile_images/me.png',
        ]);

        $this->actingAs($me)
            ->get(route('mypage.profile.edit'))
            ->assertOk()
            // input value に初期値が入っている
            ->assertSee('value="既存ユーザー"', false)
            ->assertSee('value="111-2222"', false)
            ->assertSee('value="東京都千代田区1-1-1"', false)
            ->assertSee('value="皇居前101"', false)
            // アバターのプレビュー
            ->assertSee('profile_images/me.png');
    }
}
