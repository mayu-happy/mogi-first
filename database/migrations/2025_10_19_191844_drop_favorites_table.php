<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('favorites');
    }
    public function down(): void
    { /* 復元不要なら空でOK */
    }
};
