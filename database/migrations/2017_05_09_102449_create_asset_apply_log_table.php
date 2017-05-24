<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetApplyLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建资产申请操作日志表
        Schema::create('asset_apply_log', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('action',[0,1,2])->comment('0:申请,1:审核,2:登记使用');
            $table->unsignedInteger('asset_apply_id')->comment('资产申请表ID');
            $table->string('opera_type','50')->comment('操作人类型');
            $table->unsignedInteger('operator')->comment('操作人ID');
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
        Schema::drop('asset_apply_log');
    }
}
