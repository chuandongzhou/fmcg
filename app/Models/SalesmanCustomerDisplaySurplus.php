<?php

namespace App\Models;


class SalesmanCustomerDisplaySurplus extends Model
{
    protected $table = 'salesman_customer_display_surplus';

    protected $fillable = [
        'month',
        'total',
        'surplus',
        'mortgage_goods_id',
        'salesman_customer_id'
    ];

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mortgageGoods()
    {
        return $this->belongsTo('App\Models\MortgageGoods');
    }

    /**
     * 格式化剩余量
     *
     * @return int|mixed
     */
    public function getSurplusAttributes()
    {
        return $this->mortgage_goods_id == 0 ? $this->surplus : intval($this->surplus);
    }
}
