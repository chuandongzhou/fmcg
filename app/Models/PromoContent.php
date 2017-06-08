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

    /**
     * 关联商品
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }
}
