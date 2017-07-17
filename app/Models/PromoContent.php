<?php

namespace App\Models;


class PromoContent extends Model
{
    protected $table = 'promo_content';
    protected $fillable = [
        'type',
        'goods_id',
        'quantity',
        'unit',
        'money',
        'custom',
    ];
    public $appends = ['pieces_name','goods_name'];

    protected $hidden = ['goods','id','promo_id','type','created_at','updated_at'];
    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

    public function promo()
    {
        return $this->belongsTo('App\Models\Promo');
    }

    /**
     * 获得商品单位名
     * @return string
     */
    public function getPiecesNameAttribute()
    {
        if ($this->goods_id) {
            return cons()->valueLang('goods.pieces', $this->unit);
        }
    }

    public function getGoodsNameAttribute()
    {
        if ($this->goods_id) {
            return $this->goods->name;
        }
    }
}
