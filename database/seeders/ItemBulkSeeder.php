<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemBulkSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者（無ければ作成）
        $user = User::first() ?? User::factory()->create([
            'name'     => 'Demo User',
            'email'    => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        $csv = base_path('database/seeders/data/required_items.csv');
        if (is_file($csv)) {
            $rows = $this->readCsv($csv);
            foreach ($rows as $r) {
                $priceRaw = $r['price'] ?? '0';
                $norm     = mb_convert_kana((string) $priceRaw, 'n', 'UTF-8'); // １５，０００ → 15,000
                $price    = (int) preg_replace('/[^\d]/', '', $norm);          // 15,000 / ¥15000 → 15000

                $imgValue = null;
                if (!empty($r['image_url'])) {
                    try {
                        $res = Http::timeout(15)->get($r['image_url']);
                        if ($res->successful()) {
                            $ext      = pathinfo(parse_url($r['image_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $filename = Str::uuid() . '.' . $ext;
                            $relPath  = 'items/' . $filename;
                            Storage::disk('public')->put($relPath, $res->body());
                            $imgValue = $relPath;
                        } else {
                            $imgValue = $r['image_url'];
                        }
                    } catch (\Throwable $e) {
                        $imgValue = $r['image_url'];
                    }
                }

                $item = Item::create([
                    'user_id'     => $user->id,
                    'name'        => $r['name']        ?? '商品',
                    'price'       => $price,
                    'brand'       => ($r['brand']       ?? null) ?: null,
                    'description' => $r['description']  ?? '',
                    'condition'   => $r['condition']    ?? null,
                    'img_url'     => $imgValue,
                ]);

                $item = Item::updateOrCreate(
                    ['name' => $r['name']],
                    [
                        'user_id'     => $user->id,
                        'name'        => $r['name'] ?? '商品',
                        'price'       => (int)($r['price'] ?? 0),
                        'brand'       => ($r['brand'] ?? null) ?: null,
                        'description' => $r['description'] ?? '',
                        'condition'   => $r['condition'] ?? null,
                        'img_url'     => $relPath,
                    ]
                );

                if (Schema::hasTable('categories')) {
                    $catIds = \DB::table('categories')->pluck('id')->all();
                    if ($catIds) {
                        $item->categories()->sync([$catIds[array_rand($catIds)]]);
                    }
                }
            }
        }

        Item::factory()->count(20)->create([
            'user_id' => $user->id,
            'img_url' => null,
        ]);
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        $fh   = fopen($path, 'r');
        if (!$fh) return $rows;

        $header = null;
        while (($cols = fgetcsv($fh)) !== false) {
            if ($header === null) {
                if (isset($cols[0])) {
                    $cols[0] = preg_replace('/^\xEF\xBB\xBF/', '', $cols[0]);
                }
                $header = $cols;
                continue;
            }
            if (count($cols) !== count($header)) {
                continue;
            }
            $rows[] = array_combine($header, $cols);
        }
        fclose($fh);

        return $rows;
    }
}
