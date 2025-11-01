<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $hasIndex = DB::select("
            SELECT 1
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'purchases'
              AND INDEX_NAME = 'purchases_item_id_unique'
            LIMIT 1
        ");

        if (!$hasIndex) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->unique('item_id', 'purchases_item_id_unique');
            });
        }
    }

    public function down(): void
    {
        $hasIndex = DB::select("
            SELECT 1
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'purchases'
              AND INDEX_NAME = 'purchases_item_id_unique'
            LIMIT 1
        ");

        if ($hasIndex) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropUnique('purchases_item_id_unique');
            });
        }
    }
};
