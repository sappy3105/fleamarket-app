## アプリケーション名

フリマアプリ

## 環境構築

**Docker ビルド**

- git clone git@github.com:sappy3105/contact-form-test.git
- cd contact-form-test
- docker-compose up -d --build

**Laravel 環境構築**

- docker-compose exec php bash
- composer install
- cp .env.example .env
- [Stripe ダッシュボード](https://dashboard.stripe.com/test/apikeys)（テストモード）にログインし、以下のキーを取得します。
    * **公開可能キー** (Publishable key): `pk_test_...`
    * **シークレットキー** (Secret key): `sk_test_...`
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
users ||--o| profiles : "1:1 (has one)"
users ||--o{ items : "1:N (exhibits)"
users ||--o{ sold_items : "1:N (buys)"
users ||--o{ comments : "1:N (writes)"
users ||--o{ likes : "1:N (likes)"

items ||--|{ category_item : "1:N (belongs to)"
categories ||--|{ category_item : "1:N (contains)"
items ||--o| sold_items : "1:1 (is sold as)"
items ||--o{ comments : "1:N (has)"
items ||--o{ likes : "1:N (has)"

sold_items ||--o| shipping_addresses : "1:1 (ships to)"

users {
    bigint_unsigned id PK
    string name
    string email
    timestamp email_verified_at
    string password
    string remember_token
    timestamps created_at_updated_at
}

profiles {
    bigint_unsigned id PK
    bigint_unsigned user_id FK
    string image_path
    string postcode
    string address
    string building
    timestamps created_at_updated_at
}

items {
    bigint_unsigned id PK
    bigint_unsigned user_id FK
    string image_path
    tinyInteger condition "1:良好 2:目立った傷や汚れなし 3:やや傷や汚れあり 4:状態が悪い"
    string name
    string brand_name
    text description
    integer price
    timestamps created_at_updated_at
}

categories {
    bigint_unsigned id PK
    string name
    timestamps created_at_updated_at
}

category_item {
    bigint_unsigned id PK
    bigint_unsigned item_id FK
    bigint_unsigned category_id FK
    timestamps created_at_updated_at
}

sold_items {
    bigint_unsigned id PK
    bigint_unsigned item_id FK "Unique"
    bigint_unsigned user_id FK
    tinyInteger payment_method "1:コンビニ 2:カード"
    string stripe_checkout_id
    timestamps created_at_updated_at
}

shipping_addresses {
    bigint_unsigned id PK
    bigint_unsigned sold_item_id FK
    string postcode
    string address
    string building
    timestamps created_at_updated_at
}

comments {
    bigint_unsigned id PK
    bigint_unsigned user_id FK
    bigint_unsigned item_id FK
    text content
    timestamps created_at_updated_at
}

likes {
    bigint_unsigned id PK
    bigint_unsigned user_id FK
    bigint_unsigned item_id FK
    timestamps created_at_updated_at
}
```
