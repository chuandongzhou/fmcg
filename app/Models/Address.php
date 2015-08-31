<?php

namespace App\Models;


class Address extends Model
{
    protected $table = 'address';
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
