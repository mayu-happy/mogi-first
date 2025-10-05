<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1つずつ存在チェックしてから追加（既にあればスキップ）
        if (!Schema::hasColumn('purchases', 'shipping_postal_code')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('shipping_postal_code', 20)->nullable()->after('user_id');
            });
        }

        if (!Schema::hasColumn('purchases', 'shipping_address')) {
            Schema::table('purchases', function (Blueprint $table) {
                // 前の列がない環境でも安全に after は気にせず追加
                $table->string('shipping_address', 255)->nullable();
            });
        }

        if (!Schema::hasColumn('purchases', 'shipping_building')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('shipping_building', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        // 存在する時だけ削除
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'shipping_building')) {
                $table->dropColumn('shipping_building');
            }
            if (Schema::hasColumn('purchases', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }
            if (Schema::hasColumn('purchases', 'shipping_postal_code')) {
                $table->dropColumn('shipping_postal_code');
            }
        });
    }
};
