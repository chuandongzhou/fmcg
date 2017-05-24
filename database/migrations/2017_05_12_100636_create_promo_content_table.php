<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_content',function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('promo_id')->comment('促销表ID');
            $table->enum('type',[0,1])->comment('促销表ID');
            $table->unsignedInteger('goods_id')->default(null)->comment('商品ID');
            $table->unsignedInteger('quantity')->default(null)->comment('数量');
            $table->unsignedInteger('unit')->default(null)->comment('单位');
            $table->unsignedInteger('money')->default(null)->comment('金额');
            $table->string('custom')->default(null)->comment('自定义');
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
        Schema::drop('promo_content');
    }
}
