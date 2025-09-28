<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MergeImageColumnsInItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1) img_url が無いなら追加（nullable でOK）
        if (!Schema::hasColumn('items', 'img_url')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('img_url')->nullable()->after('image_url');
            });
        }

        // 2) image_url の値を img_url に移す（img_url が空/NULLの行だけ上書き）
        if (Schema::hasColumn('items', 'image_url')) {
            DB::table('items')
                ->whereNotNull('image_url')
                ->where(function ($q) {
                    $q->whereNull('img_url')->orWhere('img_url', '');
                })
                ->update(['img_url' => DB::raw('image_url')]);
        }

        // 3) もう不要なら image_url を削除
        if (Schema::hasColumn('items', 'image_url')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // ロールバック用に image_url を復活させてデータを戻す
        if (!Schema::hasColumn('items', 'image_url')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('img_url');
            });
        }

        DB::table('items')
            ->whereNotNull('img_url')
            ->where(function ($q) {
                $q->whereNull('image_url')->orWhere('image_url', '');
            })
            ->update(['image_url' => DB::raw('img_url')]);

        // 必要なら img_url を削除（好みで）
        // Schema::table('items', function (Blueprint $table) {
        //     $table->dropColumn('img_url');
        // });
    }
}
