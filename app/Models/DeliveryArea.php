<?php

namespace App\Models;


class DeliveryArea extends Model
{
    protected $table = 'delivery_area';
    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'province_id',
        'city_id',
        'district_id',
        'street_id',
        'detail_address',
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
}
