<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Item, Purchase};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileShowEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィールページに必要情報が表示される()
    {
        $user = User::factory()->create(['name' => 'Taro', 'image' => 'profile_images/a.png']);
        $sell = Item::factory()->count(2)->create(['user_id' => $user->id]);
        $buyItem = Item::factory()->create();
        Purchase::factory()->create(['user_id' => $user->id, 'item_id' => $buyItem->id]);

        $this->actingAs($user)
            ->get(route('mypage.profile'))
            ->assertSee('Taro')
            ->assertSee('profile_images/a.png')
            ->assertSee($sell[0]->name)
            ->assertSee($buyItem->name);
    }

    /** @test */
    public function プロフィール編集画面に初期値が表示される()
    {
        $user = User::factory()->create([
            'name' => 'Hanako',
            'postal_code' => '1230001',
            'address' => '東京都千代田区1-1-1',
            'building' => '丸ビル101',
        ]);

        $this->actingAs($user)
            ->get(route('mypage.profile.edit'))
            ->assertSee('Hanako')
            ->assertSee('1230001')
            ->assertSee('東京都千代田区1-1-1')
            ->assertSee('丸ビル101');
    }
}
