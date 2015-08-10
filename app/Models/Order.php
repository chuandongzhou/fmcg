<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    /**
     * 该订单下所有商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany('app/OrderGoods');
    }

    public function receivingAddress()
    {
        return $this->hasOne('app/ReceivingAddress', 'receiving_address_id');
    }
}
