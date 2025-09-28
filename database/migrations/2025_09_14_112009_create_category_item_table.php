<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCategoryItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('category_item', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['item_id', 'category_id']);
            $table->timestamps();
        });
        // 既存: items.category_id があるならピボットにバックフィル（任意）
        if (Schema::hasColumn('items', 'category_id')) {
            $rows = DB::table('items')
                ->whereNotNull('category_id')
                ->select('id as item_id', 'category_id')->get();
            foreach ($rows as $r) {
                DB::table('category_item')->updateOrInsert(
                    ['item_id' => $r->item_id, 'category_id' => $r->category_id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_item');
    }
}
