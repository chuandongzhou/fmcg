<?php

namespace App\Models;

class SalesmanCustomerDisplayList extends Model
{
    protected $table = 'salesman_customer_display_list';

    protected $fillable = [
        'salesman_customer_id',
        'month',
        'surplus',
        'used',
        'total',
        'salesman_visit_order_id',
        'mortgage_goods_id',
        'salesman_visit_order_id'
    ];

    /**
     * 关联订货单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\SalesmanVisitOrder', 'salesman_visit_order_id');
    }

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mortgageGoods()
    {
        return $this->belongsTo('App\Models\MortgageGoods')->withTrashed();
    }

    /**
     * 获取商品名
     *
     * @return string
     */
    public function getMortgageGoodsNameAttribute()
    {
        return $this->mortgageGoods ? $this->mortgageGoods->goods_name : '现金';
    }

    /**
     * 格式化使用量
     *
     * @return int|mixed
     */
    public function getUsedAttributes()
    {
        return $this->mortgage_goods_id == 0 ? $this->used : intval($this->used);
    }

}
