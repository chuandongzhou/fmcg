<?php

namespace App\Models;

class MortgageGoods extends Model
{
    protected $table = 'mortgage_goods';

    protected $fillable = [
        'goods_name',
        'pieces',
        'shop_id',
        'status'
    ];

    public $hidden = ['created_at', 'updated_at'];

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
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
     * 获取状态名
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return '已' . cons()->valueLang('status', $this->status);
    }
}
