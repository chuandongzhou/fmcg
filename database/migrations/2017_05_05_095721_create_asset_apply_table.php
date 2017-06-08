<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建资产申请/使用表
        Schema::create('asset_apply', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id')->comment('资产表ID');
            $table->unsignedInteger('client_id')->comment('客户商店ID');
            $table->unsignedInteger('quantity')->comment('申请数量');
            $table->string('apply_remark')->default('')->comment('申请备注');
            $table->enum('status',[0,1])->default('0')->comment('0:未处理,1:通过');
            $table->unsignedInteger('salesman_id')->comment('业务员ID');
            $table->date('use_date')->default(null)->comment('开始使用日期');
            $table->timestamps();
            $table->softDeletes()->comment('拒绝');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('asset_apply');
    }
}
