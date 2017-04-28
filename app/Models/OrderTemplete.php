<?php

namespace App\Models;


class OrderTemplete extends Model
{
    protected $table = 'order_templete';


    protected $fillable = ['name', 'contact_person', 'contact_info', 'address', 'is_default'];


}
