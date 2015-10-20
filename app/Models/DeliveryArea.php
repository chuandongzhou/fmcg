<?php

namespace App\Models;


class DeliveryArea extends Model
{
    protected $table = 'delivery_area';
    protected $fillable = [
        'type',
        'addressable_id',
        'addressable_type',
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'area_name',
        'address'
    ];
    protected $hidden = [
        'type',
        'addressable_type',
        'addressable_id',
        'created_at',
        'updated_at'
    ];

    /**
     * 复用模型关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * 获取地址详情
     *
     * @return string
     */
    public function getAddressNameAttribute()
    {
        return $this->area_name . $this->address;
    }
}
