<?php

namespace App\Models;


class ShippingAddressSnapshot extends Model
{
    protected $table = 'shipping_address_snapshot';
    public $timestamps = false;
    protected $fillable = [
        'consigner',
        'phone',
        'is_default',
        'user_id',
        'x_lng',
        'y_lat'
    ];

    /**
     * 关联地址
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address()
    {
        return $this->morphOne('App\Models\AddressData', 'addressable');
    }

    public function getAddressNameAttribute()
    {
        return $this->address->address_name;
    }
}
