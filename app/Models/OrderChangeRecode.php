<?php

namespace App\Models;


class OrderChangeRecode extends Model
{
    protected $table = 'order_change_record';
    protected $fillable = [
        'user_id',
        'order_id',
        'content',
        'operate'
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
     * 用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 配送员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'user_id');
    }

    /**
     * 仓管
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function warehouseKeeper()
    {
        return $this->belongsTo(WarehouseKeeper::class, 'user_id');
    }

    /**
     * 获取修改人名
     *
     * @return mixed
     */
    public function getNameAttribute()
    {
        return !is_null($this->{$this->operate}) ? ($this->operate == 'user' ? $this->{$this->operate}->shop_name : $this->{$this->operate}->name) : '不知道';
    }
}
