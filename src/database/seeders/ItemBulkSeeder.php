<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemBulkSeeder extends Seeder
{
    public function run(): void
    {
        // 1) 出品者（無ければ作成）
        $user = User::first() ?? User::factory()->create([
            'name'     => 'Demo User',
            'email'    => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        // 2) カテゴリ一覧
        $categoryIds = [];
        if (Schema::hasTable('categories')) {
            $categoryIds = DB::table('categories')->pluck('id')->all();
        }

        // 3) CSVがあったら読む
        $csv = base_path('database/seeders/data/required_items.csv');
        if (is_file($csv)) {
            $rows = $this->readCsv($csv);

            foreach ($rows as $r) {
                $priceRaw = $r['price'] ?? '0';
                $norm     = mb_convert_kana((string) $priceRaw, 'n', 'UTF-8');
                $price    = (int) preg_replace('/[^\d]/', '', $norm);

                $imgValue = null;
                if (!empty($r['image_url'])) {
                    $imgValue = $this->downloadOrKeep($r['image_url']);
                }

                $item = Item::updateOrCreate(
                    ['name' => $r['name'] ?? '商品'],
                    [
                        'user_id'     => $user->id,
                        'price'       => $price,
                        'brand'       => ($r['brand'] ?? null) ?: null,
                        'description' => $r['description'] ?? '',
                        'condition'   => $r['condition'] ?? null,
                        'img_url'     => $imgValue,
                    ]
                );

                if (!empty($categoryIds) && method_exists($item, 'categories')) {
                    $item->categories()->syncWithoutDetaching([$categoryIds[array_rand($categoryIds)]]);
                }
            }
        }

        // 4) 必ず入れたい固定10件
        $fixedItems = [
            [
                'name'        => '腕時計',
                'price'       => '15,000',
                'brand'       => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            ],
            [
                'name'        => 'HDD',
                'price'       => '5,000',
                'brand'       => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            ],
            [
                'name'        => '玉ねぎ3束',
                'price'       => '300',
                'brand'       => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            ],
            [
                'name'        => '革靴',
                'price'       => '4,000',
                'brand'       => null,
                'description' => 'クラシックなデザインの革靴',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            ],
            [
                'name'        => 'ノートPC',
                'price'       => '45,000',
                'brand'       => null,
                'description' => '高性能なノートパソコン',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            ],
            [
                'name'        => 'マイク',
                'price'       => '8,000',
                'brand'       => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            ],
            [
                'name'        => 'ショルダーバッグ',
                'price'       => '3,500',
                'brand'       => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            ],
            [
                'name'        => 'タンブラー',
                'price'       => '500',
                'brand'       => 'なし',
                'description' => '使いやすいタンブラー',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            ],
            [
                'name'        => 'コーヒーミル',
                'price'       => '4,000',
                'brand'       => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            ],
            [
                'name'        => 'メイクセット',
                'price'       => '2,500',
                'brand'       => null,
                'description' => '便利なメイクアップセット',
                'image_url'   => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            ],
        ];

        foreach ($fixedItems as $data) {
            $brand = $data['brand'] ?? null;
            if ($brand === '' || $brand === 'なし') {
                $brand = null;
            }

            $price = (int) preg_replace('/[^\d]/', '', (string) $data['price']);

            $item = Item::updateOrCreate(
                ['name' => $data['name']],
                [
                    'user_id'     => $user->id,
                    'price'       => $price,
                    'brand'       => $brand,
                    'description' => $data['description'],
                    'condition'   => null,
                    'img_url'     => $data['image_url'],
                ]
            );

            if (!empty($categoryIds) && method_exists($item, 'categories')) {
                $item->categories()->syncWithoutDetaching([$categoryIds[0]]);
            }
        }

        // 5) 一覧をちょっと増やしたい分
        Item::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        $fh   = fopen($path, 'r');
        if (!$fh) {
            return $rows;
        }

        $header = null;
        while (($cols = fgetcsv($fh)) !== false) {
            if ($header === null) {
                // BOM除去
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

    private function downloadOrKeep(string $url): ?string
    {
        try {
            $res = Http::timeout(10)->get($url);
            if ($res->successful()) {
                $ext      = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = Str::uuid() . '.' . $ext;
                $relPath  = 'items/' . $filename;
                Storage::disk('public')->put($relPath, $res->body());
                return $relPath;
            }
        } catch (\Throwable $e) {
            // 失敗したら下のreturnに落とす
        }

        return $url ?: null;
    }
}
