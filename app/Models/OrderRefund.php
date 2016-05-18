<?php

namespace App\Models;

class OrderRefund extends Model
{

    protected $table = 'order_refund';
    protected $fillable = [
        'reason',
        'order_id',
        'refunded_amount'
    ];


}
