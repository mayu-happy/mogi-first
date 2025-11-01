<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) ユニークの重複整理
        $hasA = $this->uniqueExists('likes', 'likes_user_id_item_id_unique');
        $hasB = $this->uniqueExists('likes', 'likes_user_item_unique');

        if ($hasA && $hasB) {
            // 2本あるので片方（後から追加された想定の B）を削除
            Schema::table('likes', function (Blueprint $t) {
                $t->dropUnique('likes_user_item_unique');
            });
        } elseif (! $hasA && ! $hasB) {
            // 1本も無ければ既定名で作成
            Schema::table('likes', function (Blueprint $t) {
                $t->unique(['user_id', 'item_id'], 'likes_user_id_item_id_unique');
            });
        }
        // 2) 外部キー：既にあれば何もしない
        if (! $this->foreignExists('likes', 'likes_user_id_foreign')) {
            Schema::table('likes', function (Blueprint $t) {
                $t->foreign('user_id', 'likes_user_id_foreign')
                    ->references('id')->on('users')->cascadeOnDelete();
            });
        }
        if (! $this->foreignExists('likes', 'likes_item_id_foreign')) {
            Schema::table('likes', function (Blueprint $t) {
                $t->foreign('item_id', 'likes_item_id_foreign')
                    ->references('id')->on('items')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // down は“このマイグレーションで作ったもの”だけを戻すイメージでOK
        if ($this->uniqueExists('likes', 'likes_user_id_item_id_unique')) {
            Schema::table('likes', fn(Blueprint $t) => $t->dropUnique('likes_user_id_item_id_unique'));
        }
        if ($this->foreignExists('likes', 'likes_user_id_foreign')) {
            Schema::table('likes', fn(Blueprint $t) => $t->dropForeign('likes_user_id_foreign'));
        }
        if ($this->foreignExists('likes', 'likes_item_id_foreign')) {
            Schema::table('likes', fn(Blueprint $t) => $t->dropForeign('likes_item_id_foreign'));
        }
    }

    /* helpers */
    private function uniqueExists(string $table, string $indexName): bool
    {
        $sql = "SELECT 1
                  FROM information_schema.statistics
                 WHERE table_schema = DATABASE()
                   AND table_name   = ?
                   AND index_name   = ?
                   AND non_unique   = 0
                 LIMIT 1";
        return (bool) DB::selectOne($sql, [$table, $indexName]);
    }
    private function foreignExists(string $table, string $fkName): bool
    {
        $sql = "SELECT 1
                  FROM information_schema.table_constraints
                 WHERE table_schema = DATABASE()
                   AND table_name   = ?
                   AND constraint_type = 'FOREIGN KEY'
                   AND constraint_name = ?
                 LIMIT 1";
        return (bool) DB::selectOne($sql, [$table, $fkName]);
    }
};
