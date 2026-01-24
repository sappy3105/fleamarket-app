<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // 出品者ID（usersテーブルと紐付け）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            //画像の保存先
            $table->string('image_path')->comment('商品画像');

            // 商品の状態(1=良好、2=目立った傷や汚れなし、3=やや傷や汚れあり、4=状態が悪い)
            $table->tinyInteger('condition')->comment('1:良好 2:目立った傷や汚れなし 3:やや傷や汚れあり 4:状態が悪い');

            // 商品名と説明
            $table->string('name'); // 商品名
            $table->string('brand_name')->nullable(); // ブランド名
            $table->text('description'); // 商品説明
            $table->integer('price'); // 価格

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
