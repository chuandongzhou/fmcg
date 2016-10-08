<?php

namespace App\Models;


class OrderGoods extends Model
{
    protected $table = 'order_goods';
    public $timestamps = false;
    protected $fillable = [
        'goods_id',
        'type',
        'price',
        'num',
        'total_price',
        'pieces',
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

    /**
     * 获取单位名
     *
     * @return string
     */
    public function getPiecesNameAttribute()
    {
        return cons()->valueLang('goods.pieces', $this->pieces);
    }

    /**
     * 获取商品图片
     *
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->goods ? $this->goods->image_url : asset('images/goods_default.png');
    }
}
