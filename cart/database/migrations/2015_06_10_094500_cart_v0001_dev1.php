<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CartV0001Dev1 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_products', function ($table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->integer('price')->nullable()->index();
            $table->string('image', 256)->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->boolean('active')->default(1);
        });
        
        Schema::create('cart_carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->string('sess_id', 64)->nullable();
            $table->smallInteger('items')->default(0);
        });
        
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned();
            $table->smallInteger('quantity')->unsigned();
            $table->float('price');
        });
        
        Schema::create('cart_cart_item', function (Blueprint $table) {
            $table->bigInteger('cart_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
        });
        
        Schema::create('cart_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->unsigned();
            $table->string('shipto_fullname')->nullable();
            $table->string('shipto_email')->nullable();
            $table->string('shipto_mobile')->nullable();
            $table->string('shipto_address')->nullable();
            $table->text('comment')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamps();
            $table->string('status')->default('new');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cart_cart_item');
        Schema::drop('cart_items');
        Schema::drop('cart_orders');
        Schema::drop('cart_carts');
        Schema::drop('cart_products');
    }
}
