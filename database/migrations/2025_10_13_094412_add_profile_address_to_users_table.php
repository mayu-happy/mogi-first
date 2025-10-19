<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileAddressToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ハイフン込みで 8～10 文字想定。インデックス不要
            $table->string('postal_code', 10)->nullable()->after('email');
            $table->string('address', 255)->nullable()->after('postal_code');
            $table->string('building', 255)->nullable()->after('address');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'address', 'building']);
        });
    }
}
