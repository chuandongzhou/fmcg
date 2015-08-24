<?php

namespace App\Models;


class DataStatistics extends Model
{
    protected $table = 'data_statistics';
    public $timestamps = false;
    protected $fillable = [
        'active_user',
        'wholesalers_login_num',
        'retailer_login_num',
        'supplier_login_num',
        'wholesalers_reg_num',
        'retailer_reg_num',
        'supplier_reg_num'
    ];
}
