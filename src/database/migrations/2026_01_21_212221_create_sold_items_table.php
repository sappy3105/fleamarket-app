<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoldItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sold_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->unique()->constrained()->cascadeOnDelete(); // 1商品は1度しか売れない
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 購入者
            $table->tinyInteger('payment_method')->comment('1:コンビニ払い 2:カード払い');
            $table->string('stripe_checkout_id')->nullable()->comment('決済ID');
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
        Schema::dropIfExists('sold_items');
    }
}
