<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // image が null で avatar が入っている行は image にコピー
        DB::table('users')
            ->whereNull('image')
            ->whereNotNull('avatar')
            ->update(['image' => DB::raw('avatar')]);

        // avatar を消す（不要なら）
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
        });

        // 逆コピー（必要なら）
        DB::table('users')
            ->whereNull('avatar')
            ->whereNotNull('image')
            ->update(['avatar' => DB::raw('image')]);
    }
};
