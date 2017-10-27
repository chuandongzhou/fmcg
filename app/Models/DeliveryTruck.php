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
}
