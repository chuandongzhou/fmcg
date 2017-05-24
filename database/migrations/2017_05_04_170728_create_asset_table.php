<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建资产管理表
        Schema::create('asset', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id')->comment('资产拥有者');
            $table->string('name',60)->default(null)->comment('资产名称');
            $table->unsignedInteger('quantity')->default(null)->comment('数量');
            $table->string('unit',4)->default('')->comment('数量单位');
            $table->string('condition',100)->default(null)->comment('申请条件');
            $table->string('remark')->default('')->comment('备注');
            $table->enum('status',[0,1])->default('1')->comment('1:启用,0:停用');
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
        Schema::drop('asset');
    }
}
