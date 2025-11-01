<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'payment_method')) {
                $table->string('payment_method', 20)->default('conbini')->after('price');
            }
            if (!Schema::hasColumn('purchases', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('purchases', 'address')) {
                $table->string('address', 255)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('purchases', 'building')) {
                $table->string('building', 255)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'building')) $table->dropColumn('building');
            if (Schema::hasColumn('purchases', 'address'))  $table->dropColumn('address');
            if (Schema::hasColumn('purchases', 'postal_code')) $table->dropColumn('postal_code');
            if (Schema::hasColumn('purchases', 'payment_method')) $table->dropColumn('payment_method');
        });
    }
};
