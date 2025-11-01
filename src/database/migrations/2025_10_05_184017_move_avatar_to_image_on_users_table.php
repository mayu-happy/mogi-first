<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) まず image カラムを確実に作る
        if (!Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                // 位置は任意。既存に合わせて after(...) したい場合は対象カラムがあるかチェックしてからにしてね
                $table->string('image')->nullable();
            });
        }

        // 2) avatar -> image へデータ移行（両方ある時だけ・NULL/非NULL安全）
        if (Schema::hasColumn('users', 'avatar') && Schema::hasColumn('users', 'image')) {
            DB::table('users')
                ->whereNull('image')
                ->whereNotNull('avatar')
                ->update(['image' => DB::raw('avatar')]);
        }

        // 3) avatar カラムを落とす場合はガードしてから
        //    まだ使っているならこのブロックはコメントアウトでOK
        /*
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
        */
    }

    public function down(): void
    {
        // 逆方向（image -> avatar）に戻す場合の例
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable();
            });
        }

        if (Schema::hasColumn('users', 'image') && Schema::hasColumn('users', 'avatar')) {
            DB::table('users')
                ->whereNull('avatar')
                ->whereNotNull('image')
                ->update(['avatar' => DB::raw('image')]);
        }

        // image を落とす場合
        /*
        if (Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
        */
    }
};
