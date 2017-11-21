<?php

namespace App\Models;

class DispatchTruckReturnOrder extends Model
{
    protected $table = 'dispatch_truck_return_order';
    protected $fillable = [
        'dispatch_truck_id',
        'goods_id',
        'order_id',
        'num',
        'pieces',
    ];
    
    /**
     * 关联商品模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

    /**
     * 获取商品名
     *
     * @return mixed
     */
    public function getGoodsNameAttribute()
    {
        return $this->goods->name;
    }
}
