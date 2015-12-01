<?php

namespace App\Models;


use Carbon\Carbon;

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
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::creating(function ($model) {
            // 删除所有关联文件
            $model->created_at = (new Carbon())->subDay();
        });
    }

    /**
     * 设置活跃用户
     *
     * @param $activeUser
     */
    public function setActiveUserAttribute($activeUser)
    {
        if ($activeUser) {
            $this->attributes['active_user'] = implode(',', $activeUser);
        }

    }

    /**
     * 获取活跃用户
     *
     * @param $activeUser
     * @return array
     */
    public function getActiveUserAttribute($activeUser)
    {
        return explode(',', $activeUser);
    }
}
