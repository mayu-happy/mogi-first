## アプリケーション名

* フリマアプリ **Mogi First**

Quick Start（最短：起動→ログイン→テスト）
# 0) 取得＆起動
git clone git@github.com:mayu-happy/mogi-first.git mogi-first
cd mogi-first
docker compose up -d --build

# 1) Laravelセットアップ（依存・.env・キー・マイグレ・ストレージ）
docker compose exec php bash -lc '
  composer install && cp -n .env.example .env &&
  php artisan key:generate &&
  php artisan migrate &&
  php artisan storage:link
'

# 2) サンプルユーザー投入（Seeder）
docker compose exec php bash -lc "php artisan db:seed --class=TestUserSeeder"

# 3) テスト（任意：詳細表示つき）
docker compose exec php bash -lc "php artisan test"
# docker compose exec php bash -lc "vendor/bin/phpunit --testdox"


ブラウザ: http://localhost

ログイン用アカウント:

テスト 花子: so.happy0713@gmail.com / password12345678

テスト 次郎: test@example.com / password12345678

環境構築（Docker → Laravel まで：詳細手順）
1) リポジトリ取得（SSH）
git clone git@github.com:mayu-happy/mogi-first.git mogi-first
cd mogi-first

2) Docker ビルド & 起動
docker compose up -d --build

3) Laravel 環境構築（コンテナ内）
docker compose exec php bash


依存導入と .env 作成：

composer install
cp .env.example .env


.env の DB 設定例：

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass


キー生成・マイグレーション・ストレージ公開：

php artisan key:generate
php artisan migrate
php artisan storage:link

サンプルユーザー投入（Seeder）

database/seeders/TestUserSeeder.php を作成して、以下を貼り付けてください。

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'テスト 花子', 'email' => 'so.happy0713@gmail.com'],
            ['name' => 'テスト 次郎', 'email' => 'test@example.com'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password12345678'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}


適用コマンド：

docker compose exec php bash -lc "php artisan db:seed --class=TestUserSeeder"

PHPUnit テスト実行
php artisan test


詳細表示が欲しい場合：

vendor/bin/phpunit --testdox

テスト環境の .env（.env.testing）（任意）

テストDBを明示的に用意したい場合は以下を実行：

# .env.testing を作成（存在しない場合のみ）
docker compose exec php bash -lc "php -r \"file_exists('.env.testing') || copy('.env', '.env.testing');\""

# テスト用DBを作成（rootパスは docker-compose の MYSQL_ROOT_PASSWORD に依存）
docker compose exec mysql bash -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'

# .env.testing のDB名を調整
docker compose exec php bash -lc "sed -i -e 's/^DB_DATABASE=.*/DB_DATABASE=laravel_test/' .env.testing"

# （必要なら）テスト用マイグレーション
docker compose exec php bash -lc "php artisan migrate --env=testing"


.env.testing のサンプル：

APP_ENV=testing
APP_KEY=base64:dummykeyfordevonly0123456789abcdef=
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_test
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=log

開発環境 URL

ホーム: http://localhost

会員登録: http://localhost/register

phpMyAdmin: http://localhost:8080

使用技術（実行環境）

PHP 8.1

Laravel 10

MySQL 8.0.26

nginx / php-fpm（Docker）

認証: Laravel Fortify

テスト: PHPUnit

ER 図（Mermaid）

制約メモ：

likes: UNIQUE (user_id, item_id)、users/items 外部キーは ON DELETE CASCADE

purchases: UNIQUE (item_id)（1商品は1回のみ購入）

erDiagram
  USERS {
    bigint id PK
    string name
    string email
    string password
    string image
    string postal_code
    string address
    string building
    datetime email_verified_at
    datetime created_at
    datetime updated_at
  }

  ITEMS {
    bigint id PK
    bigint user_id FK
    string name
    text description
    int price
    string brand
    string condition
    string img_url
    datetime created_at
    datetime updated_at
  }

  ITEM_IMAGES {
    bigint id PK
    bigint item_id FK
    string path
    boolean is_main
    datetime created_at
    datetime updated_at
  }

  CATEGORIES {
    bigint id PK
    string name
    datetime created_at
    datetime updated_at
  }

  CATEGORY_ITEM {
    bigint id PK
    bigint item_id FK
    bigint category_id FK
    datetime created_at
    datetime updated_at
  }

  COMMENTS {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    text body
    datetime created_at
    datetime updated_at
  }

  LIKES {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    datetime created_at
    datetime updated_at
  }

  PURCHASES {
    bigint id PK
    bigint user_id FK
    bigint item_id FK
    int price
    string postal_code
    string address
    string building
    int payment_method
    int status
    datetime created_at
    datetime updated_at
  }

  USERS ||--o{ ITEMS : sells
  USERS ||--o{ COMMENTS : writes
  USERS ||--o{ LIKES : likes
  USERS ||--o{ PURCHASES : buys

  ITEMS ||--o{ ITEM_IMAGES : has
  ITEMS ||--o{ COMMENTS : has
  ITEMS ||--o{ LIKES : liked_by
  ITEMS ||--o{ PURCHASES : purchased_once
  ITEMS }o--o{ CATEGORIES : via_CATEGORY_ITEM

