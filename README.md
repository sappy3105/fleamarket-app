# フリマアプリ

## 環境構築

### Docker ビルド

```bash
git clone git@github.com:sappy3105/fleamarket-app.git
cd fleamarket-app
docker-compose up -d --build
```

### Laravel 環境構築

```bash
docker-compose exec php bash
composer install
cp .env.example .env
```

`.env`ファイルに以下の環境変数を追加してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

### STRIPE決済システムの設定

本プロジェクトでは決済機能に [Stripe](https://stripe.com/jp) を使用しています。
開発環境（テストモード）で動作させるには、以下の手順でキーを設定してください。

**1. APIキーの取得**

1. [Stripe公式サイト](https://stripe.com/jp)でアカウントを作成します。
2. [Stripeダッシュボード](https://dashboard.stripe.com/login)にログインし、画面左上のアカウント名が「サンドボックス（またはテスト用のアカウント名）」になっていることを確認します。
3. 画面上部の「検索」窓に「APIキー」と入力し、検索結果から「開発者 ＞ APIキー」を選択します。
4. 表示された以下の2つのキーを控えておきます。
   - **公開可能キー** (Publishable key) : `pk_test_...`
   - **シークレットキー** (Secret key) : `sk_test_...`

**2. 環境設定 (.env)**

プロジェクト直下の `.env` ファイルに、取得したキーを反映させてください。

```env
STRIPE_KEY=pk_test_あなたの公開可能キー
STRIPE_SECRET=sk_test_あなたのシークレットキー
```

### 開発環境でのメール認証テスト (Mailtrap)

本プロジェクトでは、メール認証のテストに [Mailtrap](https://mailtrap.io/) を使用しています。
機能を再現するには、以下の手順で設定を行ってください。

**1. Mailtrap のセットアップ**

1. [Mailtrap公式サイト](https://mailtrap.io/)でアカウントを作成します。
2. ログイン後、左メニューの「Sandboxes」→「My Sandbox」をクリックします。
3. 中央の「Integration」タブが選択されていることを確認し、その下の「SMTP」を選択します。
4. 表示された `Credentials` 欄の `Username` と `Password` を確認します。

**2. 環境設定 (.env)**

プロジェクト直下の `.env` ファイルに、確認した値を反映させてください。

```env
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=（確認したユーザー名）
MAIL_PASSWORD=（確認したパスワード）
```

### 環境変数の反映

Laravel環境構築およびStripe、Mailtrapの設定を `.env` に追記した後は、設定をアプリケーションに反映させるため、必ず以下のコマンドを実行してください。

```bash
docker-compose exec php bash
php artisan config:clear
```

### アプリケーションキーの作成

```bash
php artisan key:generate
```

### マイグレーションの実行

```bash
php artisan migrate
```

### シーディングの実行

```bash
php artisan db:seed
```

### フロントエンド環境構築
本プロジェクトでは Autoprefixer を使用して CSS のブラウザ互換性を管理しています。スタイルを正しく反映させるため、以下の手順を実行してください。

```bash
# 1. 依存パッケージ（Autoprefixer等）のインストール
npm install

# 2. アセットのコンパイル（ビルド）
# 初回実行時、Autoprefixer等の追加インストールを求められた場合は、指示に従い再度実行してください。
npm run dev
```
もし `npm run dev` でエラーが出る場合は、以下のコマンドを試してから再度ビルドしてください。
```bash
npm install postcss-loader autoprefixer --save-dev
```

### ストレージリンクの作成
商品画像などのアップロードファイルを表示するために、ストレージへのシンボリックリンクを作成する必要があります。

```bash
php artisan storage:link
```

## 使用技術（実行環境）

- PHP 8.1.34
- Laravel 8.83.29
- MySQL 8.0.26
- nginx 1.21.1

## URL

- 開発環境： http://localhost/
- phpMyAdmin： http://localhost:8080/

## 動作確認ガイド（テスト用データ構成）
セットアップ完了後、以下のテストデータを使用して各機能を確認いただけます。

### 動作確認用データの構成

本プロジェクトでは、リレーションを考慮した10パターンのテストデータを投入しています。GitHub経由で動作確認を行う際は、以下の手順で環境を構築し、テストシナリオに沿ってご確認ください。

**1. データの初期化手順**
リポジトリをクローンし、環境構築（`.env`設定等）が完了した後、以下のコマンドを実行してデータベースを最新の状態にします。
```bash
php artisan migrate:fresh --seed
```

**2. テスト用アカウント**
動作確認には以下の固定ユーザーを使用してください。パスワードは全て共通です。

- テストユーザー1 : 認証済み
  test1@example.com / password

- テストユーザー2 : 未認証・認証メール送信済み
  test2@example.com / password

- テストユーザー3 : 未認証・認証メール未送信
  test3@example.com / password

**3. 商品データとテスト一覧**
商品一覧および各商品詳細ページにて、以下の挙動を確認できます。

| ID | 商品名 | 出品者 | 購入者 | 支払い状況 | いいね/コメ数 | 確認事項 |
| :--- | :--- | :---: | :---: | :---: | :--- |
| 1 | 腕時計 | ユーザー1 | - | 支払済 | 0 / 0 | 初期状態の確認 |
| 2 | HDD | ユーザー1 | - | 支払済 | 1 / 0 | 出品者本人による「いいね」の反映確認 |
| 3 | 玉ねぎ3束 | ユーザー1 | ユーザー2 | 支払済 | 2 / 0 | **売却済み（SOLD）**商品の表示と購入ボタンの無効化 |
| 4 | 革靴 | ユーザー1 | ユーザー3 | 支払い待ち | 0 / 1 | 購入者による購入前の質問コメント表示 |
| 5 | ノートPC | ユーザー2 | - | 支払済 | 0 / 2 | 複数ユーザーによるコメント投稿のレイアウト確認 |
| 6 | マイク | ユーザー2 | ユーザー1 | 支払済 | 1 / 1 | 自分が購入した商品のステータスと過去のアクション確認 |
| 7 | ショルダーバッグ | ユーザー2 | ユーザー3 | 支払済 | 1 / 1 | 出品者と購入者間での相互アクション確認 |
| 8 | タンブラー | ユーザー3| - | 支払済 | 2 / 1 | 自分が行ったいいね・コメントのUI反映（ハートの色等） |
| 9 | コーヒーミル | ユーザー3 | ユーザー1 | 支払い待ち | 1 / 2 | 購入確定までのやり取り（複数コメント）の再現確認 |
| 10 | メイクセット | ユーザー3 | ユーザー2 | 支払済 | 2 / 2 | 全要素（いいね・コメント・SOLD）が揃った状態の確認 |

## ER 図

```mermaid
erDiagram
users ||--o| profiles : ""
users ||--o{ items : ""
users ||--o{ sold_items : ""
users ||--o{ comments : ""
users ||--o{ likes : ""

items ||--o{ category_item : ""
categories ||--o{ category_item : ""
items ||--o| sold_items : "sold"
items ||--o{ comments : ""
items ||--o{ likes : ""

sold_items ||--|| shipping_addresses : ""

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
    tinyint condition "1:Excellent 2:Good 3:Fair 4:Poor"
    varchar name
    varchar brand_name
    text description
    unsigned_integer price
    timestamp created_at
    timestamp updated_at
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
    tinyint payment_method "1:convenience 2:card"
    varchar status
    varchar stripe_checkout_id
    timestamp created_at
    timestamp updated_at
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
