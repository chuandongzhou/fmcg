<?php

namespace App\Models;


use Carbon\Carbon;

class Promoter extends Model
{
    protected $table = 'promoter';
    protected $fillable = [
        'name',
        'contact',
        'start_at',
        'end_at',
        'spreading_code'
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册创建事件
        static::created(function ($model) {
            $model->spreading_code = 'DBD' . str_pad($model->id, 3, "0", STR_PAD_LEFT);
            $model->save();
        });
    }

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shops()
    {
        return $this->hasMany('App\Models\Shop', 'spreading_code', 'spreading_code');
    }

    /**
     * 获取当前广告状态
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        $startAt = new Carbon($this->start_at);
        if ($startAt->isFuture()) {
            return '未开始';
        }

        $endAt = new Carbon($this->end_at);
        if (is_null($endAt)) {
            return '永久';
        }

        if ($endAt->isPast()) {
            return '已结束';
        }

        return '展示中';
    }
}
