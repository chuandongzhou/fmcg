<?php

namespace App\Models;


class Promoter extends Model
{
    protected $table = 'promoter';
    protected $fillable = [
        'name',
        'contact',
        'spreading_code'
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册创建事件
        static::creating(function ($model) {
           $model->spreading_code = strtoupper(uniqid());
        });
    }
}
