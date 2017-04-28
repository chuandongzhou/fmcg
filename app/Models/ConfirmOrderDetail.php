<?php

namespace App\Models;

class ConfirmOrderDetail extends Model
{
    protected $table = 'confirm_order_detail';
    protected $fillable = [
        'shop_id',
        'customer_id',
        'goods_id',
        'price',
        'pieces'
    ];

}