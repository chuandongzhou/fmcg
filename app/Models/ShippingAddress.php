<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use SoftDeletes;
    protected $table = 'shipping_address';
    protected $fillable = [
        'consigner',
        'phone',
        'is_default',
        'user_id',
        'x_lng',
        'y_lat'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
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
        return $this->morphOne('App\Models\AddressData', 'addressable');
    }
}
