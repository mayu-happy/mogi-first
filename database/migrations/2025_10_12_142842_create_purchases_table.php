<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete(); // 1商品1購入を想定
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 購入者
            $table->unsignedInteger('price');                               // 購入時価格
            $table->string('status')->default('paid');                       // paid/cancelled 等
            $table->timestamps();

            $table->unique('item_id');                                      // 同じ商品を重複購入させない
            $table->index(['user_id', 'created_at']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
}
