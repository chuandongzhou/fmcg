<?php

namespace App\Models;


class OrderOperateRecord extends Model
{
    protected $table = 'order_operate_record';
    protected $fillable = [
        'operate_id',
        'order_id',
        'operater', //名字
        'time',
        'operate_name',
        'type'
    ];
    public $timestamps = false;
}
