<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 画像用の列を追加（なければ）
        if (!Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('image')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        // このファイルで追加した列だけを戻す
        if (Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
