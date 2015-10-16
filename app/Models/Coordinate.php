<?php

namespace App\Models;

class Coordinate extends Model
{
    protected $table = 'coordinate';
    protected $fillable = [
        'delivery_area_id',
        'al_lng',
        'al_lat',
        'rl_lng',
        'rl_lat'
    ];

    public $timestamps = false;

    public function deliveryArea()
    {
        return $this->belongsTo('App\Models\DeliveryArea', 'delivery_area_id', 'id');
    }
}
