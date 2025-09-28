<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandImageurlConditionToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // 価格の後ろにブランド名（任意）
            $table->string('brand')->nullable()->after('price');

            // 説明の後ろに画像URL（長め・任意）
            $table->string('image_url', 2048)->nullable()->after('description');

            // 画像URLの後ろにコンディション（任意）
            $table->string('condition', 50)->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['brand', 'image_url', 'condition']);
        });
    }
}
