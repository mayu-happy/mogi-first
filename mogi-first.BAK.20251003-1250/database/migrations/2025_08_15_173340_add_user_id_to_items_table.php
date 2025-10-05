<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // 既に列がない場合のみ追加（再実行に強い）
            if (!Schema::hasColumn('items', 'user_id')) {
                // users.id は bigIncrements 想定
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')   // ->references('id')->on('users')
                    ->nullOnDelete();        // ユーザー削除時は NULL
                // 必要なら ->cascadeOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
}
