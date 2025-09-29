<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 小計画面で支払い方法の選択が反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->get(route('purchase.create', $item))
            ->assertOk();

        // 更新（リダイレクトの行き先は厳密に見ない）
        $payload = ['payment_method' => 'カード支払い'];
        $this->post(route('purchase.updatePayment', $item), $payload)
            ->assertStatus(302);  // 成功してリダイレクトしてることだけ確認

        // 反映確認
        $this->get(route('purchase.create', $item))
            ->assertOk()
            ->assertSee('カード支払い');
    }
}
