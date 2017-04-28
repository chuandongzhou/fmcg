<?php

namespace App\Models;


class OrderChangeRecode extends Model
{
    protected $table = 'order_change_record';
    protected $fillable = [
        'user_id',
        'order_id',
        'content'
    ];

    protected $hidden = [
        'id',
        'order_id',
        'updated_at'
    ];


    /**
     * 订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * 获取修改人名
     *
     * @return mixed
     */
    public function getNameAttribute()
    {
        $order = $this->order;

        return $order->shop->user_id == $this->user_id ? $order->shop_name : $order->deliveryMan()->find($this->user_id)->pluck('name');
    }
}
