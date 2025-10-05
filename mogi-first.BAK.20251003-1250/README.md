## 環境構築
**Dockerビルド**
1. `git clone <リポジトリURL>`
git@github.com:mayu-happy/mogi-first.git
2. DockerDesktopアプリを立ち上げる
3. `cd mogi-first`でディレクトリ移動
4. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを コピーして「.env」を作成し、DBの設定を変更
``` text
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

**使用技術（実行環境）**
- Laravel 8.83.8
- PHP 8.x
- MySQL
- Docker / Docker Compose
- phpMyAdmin
- Faker（ダミーデータ生成）

## ER図
[ER図](mogi-first.drawio.png)

## URL
- 開発環境：http://localhost
- phpMyAdmin：http://localhost:8080