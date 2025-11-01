<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('favorites');
    }
    public function down(): void
    { /* 必要なら復元を書く */
    }
};
