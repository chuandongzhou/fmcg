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
     * ��������
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('app/shop');
    }

    /**
     * ��������
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsDeliveryArea()
    {
        return $this->hasMany('app/GoodsDeliveryArea');
    }

    /**
     * ���������Ʒ
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderGoods()
    {
        return $this->hasMany('app/OrderGoods');
    }
}