<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('purchases', 'payment_method')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('payment_method')->nullable()->after('item_id');
            });
        }
        if (!Schema::hasColumn('purchases', 'price')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->unsignedInteger('price')->nullable()->after('payment_method');
            });
        }
        if (!Schema::hasColumn('purchases', 'shipping_postal_code')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('shipping_postal_code')->nullable()->after('price');
            });
        }
        if (!Schema::hasColumn('purchases', 'shipping_address')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('shipping_address')->nullable()->after('shipping_postal_code');
            });
        }
        if (!Schema::hasColumn('purchases', 'shipping_building')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('shipping_building')->nullable()->after('shipping_address');
            });
        }
        if (!Schema::hasColumn('purchases', 'status')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->string('status')->default('paid')->after('shipping_building'); // 例
            });
        }

        // 1商品=1購入にしたいならユニーク制約（任意・既存重複がある場合は付けられません）
        // if (!Schema::hasColumn('purchases', 'item_id')) { /* already exists */ }
        // Schema::table('purchases', function (Blueprint $table) {
        //     $table->unique('item_id');
        // });
    }

    public function down(): void
    {
        foreach (['status', 'shipping_building', 'shipping_address', 'shipping_postal_code', 'price', 'payment_method'] as $col) {
            if (Schema::hasColumn('purchases', $col)) {
                Schema::table('purchases', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
        // ユニーク制約を付けた場合は外す処理も別途
        // Schema::table('purchases', function (Blueprint $table) {
        //     $table->dropUnique(['item_id']);
        // });
    }
};
