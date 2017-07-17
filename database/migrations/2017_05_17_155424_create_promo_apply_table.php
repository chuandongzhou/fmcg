<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_apply',function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('promo_id')->comment('促销表ID');
            $table->unsignedInteger('client_id')->comment('客户商店ID');
            $table->enum('status',[0,1])->default('0')->comment('审核状态 0 : 未通过,1 : 通过');
            $table->date('pass_date')->default(null)->comment('通过时间');
            $table->date('use_date')->default(null)->comment('使用日期');
            $table->unsignedInteger('salesman_id')->comment('业务员ID');
            $table->string('apply_remark')->comment('申请备注');
            $table->softDeletes()->comment('软');
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
        Schema::drop('promo_apply');
    }
}
