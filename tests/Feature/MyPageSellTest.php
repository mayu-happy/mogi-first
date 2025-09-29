<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class MyPageSellTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function マイページには自分の出品だけ表示される()
    {
        $me  = User::factory()->create();
        $you = User::factory()->create();

        $mine   = Item::factory()->for($me)->create(['name' => '自分の品']);
        $others = Item::factory()->for($you)->create(['name' => '他人の品']);

        $res = $this->actingAs($me)->get(route('mypage.index'));

        $res->assertOk();
        $res->assertSee('自分の品');
        $res->assertDontSee('他人の品');
    }
}
