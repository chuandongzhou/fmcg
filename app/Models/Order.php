<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $fillable = [
        'order_id',
        'price',
        'payable_type',
        'payable_id',
        'remark',
        'status',
        'shipping_address_id',
        'delivery_man_id',
        'user_id',
        'seller_id',
        'paid_at',
        'confirmed_at'
    ];

    /**
     * 该订单下所有商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }


    /**
     * 收货地址
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function receivingAddress()
    {
        return $this->hasOne('App\Models\ReceivingAddress');
    }
}
