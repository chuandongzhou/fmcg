<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsDeliveryArea extends Model
{
    protected $table = 'goodsDeliveryArea';
    public $timestamp = false;
    protected $fillable = [
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'detail_address',
        'goods_id'
    ];

    /**
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }
}
