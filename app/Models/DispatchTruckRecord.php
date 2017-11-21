<?php

namespace App\Models;



class DispatchTruckRecord extends Model
{
    protected $table = 'dispatch_truck_record';
    protected $fillable = [
        'delivery_truck_id',
        'name',
        'time',
        'operater',
    ];
    public $timestamps = false;
    
}
