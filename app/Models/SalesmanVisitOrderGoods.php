<?php

namespace App\Models;

class SalesmanVisitOrderGoods extends Model
{
    protected $table = 'salesman_visit_order_goods';

    protected $fillable = [
        'goods_id',
        'price',
        'num',
        'amount',
        'type',
        'salesman_visit_order_id',
        'salesman_visit_id'
    ];

    /**
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods')->withTrashed();
    }

    /**
     * 关联订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesmanVisitOrder()
    {
        return $this->belongsTo('App\Models\SalesmanVisitOrder');
    }

    /**
     * 关联商品记录表
     *
     * @return mixed
     */
    public function goodsRecord()
    {
        return $this->belongsTo('App\Models\SalesmanVisitGoodsRecord', 'salesman_visit_id',
            'salesman_visit_id')->where('goods_id', $this->goods_id);
    }

    /**
     * 获取商品名
     *
     * @return string
     */
    public function getGoodsNameAttribute()
    {
        return $this->goods ? $this->goods->name : '';
    }

    /**
     * 关联抵费商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mortgageGoods()
    {
        return $this->belongsTo('App\Models\MortgageGoods', 'goods_id', 'goods_id');
    }

    /**
     * 获取抵费商品名
     *
     * @return string
     */
    public function getMortgageGoodsNameAttribute()
    {
        return $this->mortgageGoods ? $this->mortgageGoods->goods_name : '';
    }
}
