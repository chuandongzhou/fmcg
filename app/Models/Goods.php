<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table = 'goods';
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'brand_id',
        'packing',
        'is_new',
        'is_out',
        'is_change',
        'is_back',
        'is_expire',
        'is_promotion',
        'promotion_info',
        'min_num',
        'introduce',
        'shop_id'
    ];

    /**
     * 所属店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App/shop');
    }

    /**
     * 配送区域
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsDeliveryArea()
    {
        return $this->hasMany('App\Models\GoodsDeliveryArea');
    }

    /**
     * 订单里的商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }
}