<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/6
 * Time: 16:29
 */
namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table = 'goods';

    /**
     * 所属店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('app/shop');
    }

    /**
     * 配送区域
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsDeliveryArea()
    {
        return $this->hasMany('app/GoodsDeliveryArea');
    }

    /**
     * 订单里的商品
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderGoods()
    {
        return $this->hasMany('app/OrderGoods');
    }
}