<?php

namespace App\Models;


class OrderGoods extends Model
{
    protected $table = 'order_goods';
    public $timestamps = false;
    protected $fillable = [
        'goods_id',
        'price',
        'num',
        'total_price',
        'order_id'
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
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods')->withTrashed();
    }

    public function getPiecesNameAttribute()
    {
        return cons()->valueLang('goods.pieces', $this->pieces);
    }
}
