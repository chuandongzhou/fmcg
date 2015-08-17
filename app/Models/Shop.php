<?php

namespace App\Models;

class Shop extends Model
{
    protected $table = 'shop';
    protected $timestamp = false;
    protected $fillable = [
        'contact_person',
        'contact_info',
        'introduction',
        'min_money',
        'delivery_area',
        'delivery_location',
        'user_id',
        'license_num'
    ];

    /**
     * 用户表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * 送货人列表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deliveryMans()
    {
        return $this->hasMany('App\Models\DeliveryMan');
    }

    /**
     * 商品表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany('App\Models\Goods');
    }
}
