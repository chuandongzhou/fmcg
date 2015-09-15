<?php

namespace App\Models;

class ShippingAddress extends Model
{
    //
    protected $table = 'shipping_address';
    protected $fillable = [
        'consigner',
        'phone',
        'is_default',
        'user_id'
    ];

    /**
     * 用户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 关联地址
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address()
    {
        return $this->morphOne('App\Models\DeliveryArea', 'addressable');
    }
}
