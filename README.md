## アプリケーション名

フリマアプリ

## 環境構築

**Docker ビルド**

- git clone git@github.com:sappy3105/fleamarket-app.git
- cd contact-form-test
- docker-compose up -d --build

**Laravel 環境構築**

- docker-compose exec php bash
- composer install
- cp .env.example .env
- [Stripe ダッシュボード](https://dashboard.stripe.com/test/apikeys)（テストモード）にログインし、以下のキーを取得します。
  - **公開可能キー** (Publishable key): `pk_test_...`
  - **シークレットキー** (Secret key): `sk_test_...`
- .envファイルに以下の環境変数を追加

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

STRIPE_PUBLIC_KEY=pk_test_あなたの公開可能キー
STRIPE_SECRET_KEY=sk_test_あなたのシークレットキー
```

- 設定を反映させるために、必ず以下のコマンドを実行してください。

```bash
php artisan config:clear
```

**アプリケーションキーの作成**

- php artisan key:generate

**マイグレーションの実行**

- php artisan migrate

**シーディングの実行**

- php artisan db:seed

## 使用技術（実行環境）

- PHP 8.1.34
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1

## URL

- 開発環境： http://localhost/
- phpMyAdmin： http://localhost:8080/

## ER 図

```mermaid
erDiagram
users ||--o| profiles
users ||--o{ items
users ||--o{ sold_items
users ||--o{ comments
users ||--o{ likes

items ||--o{ category_item
categories ||--o{ category_item
items ||--o| sold_items : "売却済みの場合のみ存在"
items ||--o{ comments
items ||--o{ likes

sold_items ||--|| shipping_addresses

users {
    unsigned_bigint id PK
    varchar name
    varchar email UK
    varchar password
    timestamp created_at
    timestamp updated_at
}

profiles {
    unsigned_bigint id PK
    unsigned_bigint user_id FK,UK
    varchar image_path
    varchar postcode
    varchar address
    varchar building
    timestamp created_at
    timestamp updated_at
}

categories {
    unsigned_bigint id PK
    varchar name UK
    timestamp created_at
    timestamp updated_at
}

items {
    unsigned_bigint id PK
    unsigned_bigint user_id FK
    varchar image_path
    tinyint condition "1:良好 2:目立った傷や汚れなし 3:やや傷や汚れあり 4:状態が悪い"
    string name
    string brand_name
    text description
    integer price
    timestamps created_at_updated_at
}

category_item {
    unsigned_bigint id PK
    unsigned_bigint item_id FK,UK
    unsigned_bigint category_id FK,UK
    timestamp created_at
    timestamp updated_at
}

likes {
    unsigned_bigint id PK
    unsigned_bigint user_id FK,UK
    unsigned_bigint item_id FK,UK
    timestamp created_at
    timestamp updated_at
}

comments {
    unsigned_bigint id PK
    unsigned_bigint user_id FK
    unsigned_bigint item_id FK
    text content
    timestamp created_at
    timestamp updated_at
}

sold_items {
    unsigned_bigint id PK
    unsigned_bigint item_id FK,UK
    unsigned_bigint user_id FK
    tinyint payment_method "1:コンビニ 2:カード"
    varchar stripe_checkout_id
    timestamp created_at
    timestamp updated_at
}

shipping_addresses {
    unsigned_bigint id PK
    unsigned_bigint sold_item_id FK
    string postcode
    string address
    string building
    timestamps created_at_updated_at
}

shipping_addresses {
    unsigned_bigint id PK
    unsigned_bigint sold_item_id FK,UK
    varchar postcode
    varchar address
    varchar building
    timestamp created_at
    timestamp updated_at
}
```
