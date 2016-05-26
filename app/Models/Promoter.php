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
}
