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

    /**
     * 按时间搜索
     *
     * @param $query
     * @param $dates
     * @return mixed
     */
    public function scopeOfDates($query, $dates)
    {
        return $query->where(function ($query) use ($dates) {
            if ($beginDay = array_get($dates, 'begin_day')) {
                $query->where('created_at', '>', $beginDay);
            }
            if ($endDay = array_get($dates, 'end_day')) {
                $endDay = (new Carbon($endDay))->endOfDay();
                $query->where('created_at', '<', $endDay);
            }
        });
    }
}
