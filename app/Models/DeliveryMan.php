<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMan extends Model
{
    protected $table = 'delivery_man';

    public function shop()
    {
        return $this->belongsTo('app/Shop');
    }
}
