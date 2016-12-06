<?php

namespace App\Models;


class ShopRecommendGoods extends Model
{
    protected $table = 'shop_recommend_goods';
    public $timestamps = false;
    protected $fillable = [
        'goods_id',
        'shop_id'
    ];

    /**
     * 店铺表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
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
     * 获取商品图片
     *
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->goods ? $this->goods->image_url : asset('images/goods_default.png');
    }
}
