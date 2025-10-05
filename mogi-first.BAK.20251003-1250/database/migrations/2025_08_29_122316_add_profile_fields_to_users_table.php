<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'postal_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('postal_code')->nullable()->after('email');
            });
        }

        if (!Schema::hasColumn('users', 'address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('address')->nullable()->after('postal_code');
            });
        }

        if (!Schema::hasColumn('users', 'building')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('building')->nullable()->after('address');
            });
        }

        if (!Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('image')->nullable()->after('building');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
        if (Schema::hasColumn('users', 'building')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('building');
            });
        }
        if (Schema::hasColumn('users', 'address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
        if (Schema::hasColumn('users', 'postal_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('postal_code');
            });
        }
    }
};
