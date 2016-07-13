<?php

namespace App\Models;


class SalesmanVisitGoodsRecord extends Model
{
    protected $table = 'salesman_visit_goods_record';

    protected $fillable = [
        'goods_id',
        'stock',
        'production_date'
    ];
    public $timestamps= false;
}
