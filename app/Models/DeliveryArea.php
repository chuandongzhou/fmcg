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
        'address',
        'coordinate'
    ];
    protected $hidden = [
        'type',
        'addressable_type',
        'addressable_id',
        'created_at',
        'updated_at'
    ];


    public static function boot()
    {
        parent::boot();
        // 注册删除事件
        static::deleting(function ($model) {
            $model->coordinate()->delete();

        });
    }

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

    /**
     * 配送区域的经纬度,一个区域对应一条记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function coordinate()
    {
        return $this->hasOne('App\Models\Coordinate', 'delivery_area_id', 'id');
    }
}
