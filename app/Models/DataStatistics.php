<?php

namespace App\Models;


class DataStatistics extends Model
{
    protected $table = 'data_statistics';
    public $timestamps = false;
    protected $fillable = [
        'active_user',
        'wholesaler_login_num',
        'retailer_login_num',
        'supplier_login_num',
        'wholesaler_reg_num',
        'retailer_reg_num',
        'supplier_reg_num',
        'created_at'
    ];

    /**
     * 获取活跃用户
     * @param $activeUser
     * @return array
     */
    public function getActiveUserAttribute($activeUser)
    {
        return explode(',', $activeUser);
    }
}
