<?php

namespace App\Models;

class Coordinate extends Model
{
    protected $table = 'coordinate';
    protected $fillable = [
        'delivery_area_id',
        'bl_lng',
        'bl_lat',
        'sl_lng',
        'sl_lat'
    ];

    public $timestamps = false;

    /**
     * 配送区域经纬度
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryArea()
    {
        return $this->belongsTo('App\Models\DeliveryArea', 'delivery_area_id', 'id');
    }
}
