<?php

namespace App\Models;


class DeliveryTruck extends Model
{
    protected $table = 'delivery_truck';

    protected $fillable = [
        'name',
        'license_plate',
        'shop_id',
        'status'
    ];

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


    /**
     * 车牌号大写
     *
     * @param $licensePlate
     */
    public function setLicensePlateAttribute($licensePlate)
    {
        if ($licensePlate) {
            $this->attributes['license_plate'] = strtoupper($licensePlate);
        }
    }

    /**
     * 获取卡车状态名
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return cons()->valueLang('truck.status', $this->status);
    }

    /**
     * 关联发车单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchTruck()
    {
        return $this->hasMany(DispatchTruck::class);
    }

    /**
     * 筛选启用
     *
     * @param $query
     * @return mixed
     */
    public function scopeEnable($query)
    {
        return $query->where('status', '>', cons('truck.status.forbidden'));
    }

}
