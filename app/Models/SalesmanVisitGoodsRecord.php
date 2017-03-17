<?php

namespace App\Models;


class SalesmanVisitGoodsRecord extends Model
{
    protected $table = 'salesman_visit_goods_record';

    protected $fillable = [
        'goods_id',
        'stock',
        'production_date'
    ];
    public $timestamps = false;

    public function visit()
    {
        return $this->belongsTo('App\Models\SalesmanVisit', 'salesman_visit_id');
    }

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods')->withTrashed();
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
     * 商品图片
     *
     * @return string
     */
    public function getGoodsImageAttribute()
    {
        return $this->goods ? $this->goods->image_url : asset('images/goods_default.png');
    }
}
