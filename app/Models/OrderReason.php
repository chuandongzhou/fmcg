<?php

namespace App\Models;

class OrderReason extends Model
{

    protected $table = 'order_reason';
    protected $fillable = [
        'reason',
        'order_id',
        'refunded_amount',
        'type',
    ];
}
