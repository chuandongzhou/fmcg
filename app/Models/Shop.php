<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shop';
    public $timestamp = false;

    public function user()
    {
        return $this->belongsTo('app/User');
    }

    public function deliveryMans()
    {
        return $this->hasMany('app/DeliveryMan');
    }

    public function goods()
    {
        return $this->hasMany('app/Goods');
    }
}
