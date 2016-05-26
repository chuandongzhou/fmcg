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
}
