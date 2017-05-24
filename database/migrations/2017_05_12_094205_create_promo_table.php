<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoTable extends Migration
{
    public function up()
    {
        //创建促销表
        Schema::create('promo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id')->comment('所属店铺');
            $table->string('name',100)->comment('促销名称');
            $table->enum('type',[1,2,3,4,5])->comment('条件or返利类型');
            $table->dateTime('start_at')->comment('开始时间');
            $table->dateTime('end_at')->comment('结束时间');
            $table->string('remark')->comment('备注');
            $table->enum('status',['0','1'])->default(1)->comment('状态，0：禁用，1：启用');
            $table->timestamps();
            $table->softDeletes()->comment('软删除');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('promo');
    }
}
