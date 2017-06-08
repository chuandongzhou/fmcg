<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //创建库存表
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inventory_number','60')->comment('出入库单号');
            $table->unsignedInteger('user_id')->comment('出入库人ID');
            $table->unsignedInteger('goods_id')->comment('商品ID');
            $table->unsignedInteger('shop_id')->comment('所属商铺ID');
            $table->tinyInteger('inventory_type')->default('1')->comment('出入库类型,1:系统,2:手动');
            $table->tinyInteger('action_type')->comment('操作类型,1:入库,2:出库');
            $table->unsignedInteger('order_number')->comment('订单ID');
            $table->dateTime('production_date')->comment('生产日期');
            $table->tinyInteger('pieces')->comment('出入库单位');
            $table->decimal('cost') ->default(0)->comment('成本');
            $table->unsignedInteger('quantity')->default(0)->comment('出入库数量');
            $table->unsignedInteger('surplus')->default(0)->comment('剩余数量');
            $table->string('remark')->default('')->comment('备注');
            $table->string('in_id')->default(null)->comment('具体用那条记录出的库');
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
        Schema::drop('inventory');
    }
}
