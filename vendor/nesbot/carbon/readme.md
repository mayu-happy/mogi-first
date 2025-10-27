## アプリケーション名

* フリマアプリ **Mogi First**

---

## 環境構築（Docker → Laravel まで）

### 1) リポジトリ取得（SSH）

```bash
git clone git@github.com:mayu-happy/mogi-first.git mogi-first
cd mogi-first
```


### 2) Docker ビルド & 起動

```bash
docker compose up -d --build
```


### 3) Laravel 環境構築（コンテナ内）

```bash
docker compose exec php bash
```

依存導入と .env 作成：

```bash
composer install
cp .env.example .env
```

`.env` の DB 設定：

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

キー生成・マイグレーション・ストレージ公開：

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```


---

## PHPUnit テスト実行

```bash
php artisan test
```

詳細表示が欲しい場合：

```bash
vendor/bin/phpunit --testdox
```

### テスト環境の .env（`.env.testing`）

####  MySQL

1. テストDBを作成（rootパスワードは docker-compose の環境変数に依存）

```bash
docker compose exec mysql bash -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'
```

2. `.env.testing` を作成

```ini
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
```


---

## 開発環境 URL

* ホーム: [http://localhost](http://localhost)
* 会員登録: [http://localhost/register](http://localhost/register)
* phpMyAdmin: [http://localhost:8080](http://localhost:8080)

## サンプルユーザー（ログイン用）

* テスト　花子: `so.happy0713@gmail.com` / `password12345678`
* テスト　次郎: `test@example.com` / `password12345678`


## 使用技術（実行環境）

* **PHP 8.1**
* **Laravel 10**
* **MySQL 8.0.26**
* **nginx / php-fpm（Docker）**
* 認証: **Laravel Fortify**
* テスト: **PHPUnit**

---

## ER 図

* `docs/er.png`（または `docs/er.drawio`）を参照してください。

---

