<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,   // ← 先にカテゴリ
            ItemBulkSeeder::class,   // ← その後アイテム
        ]);
    }
}
