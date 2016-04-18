<?php

namespace App\Models;


class Feedback extends Model
{
    protected $table = 'feedback';
    protected $fillable = [
        'account',
        'contact',
        'content',
        'status',
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->attributes['account'] = auth()->id() ? auth()->user()->user_name : '';
        });
    }
}
