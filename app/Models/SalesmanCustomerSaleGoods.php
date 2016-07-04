<?php

namespace App\Models;

class SalesmanCustomerSaleGoods extends Model
{
    protected $table = 'salesman_customer_sale_goods';

    protected $fillable = ['goods_id', 'salesman_customer_id'];
}
