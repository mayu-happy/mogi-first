<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ★ categories テーブルが無ければ何もしない（テスト時に安全）
        if (!Schema::hasTable('categories')) {
            return;
        }

        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedSmallInteger('sort_order')->default(999)->after('name');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }
        if (Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
