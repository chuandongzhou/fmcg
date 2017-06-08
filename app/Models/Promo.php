<?php

namespace App\Models;


class Promo extends Model
{
    protected $table = 'promo';
    protected $fillable = [
        'shop_id',
        'name',
        'type',
        'start_at',
        'end_at',
        'remark',
        'status',
    ];


    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->apply()->delete();
            $model->condition()->delete();
            $model->rebate()->delete();
            $model->rebate()->delete();
        });
    }

    /**
     * 申请记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apply()
    {
        return $this->hasMany('App\Models\PromoApply');
    }

    /**
     * 条件
     *
     * @return mixed
     */
    public function condition()
    {
        return $this->hasMany('App\Models\PromoContent')->where(function ($query) {
            $query->where('type', cons('promo.content_type.condition'));
        });
    }

    /**
     * 返利
     *
     * @return mixed
     */
    public function rebate()
    {
        return $this->hasMany('App\Models\PromoContent')->where(function ($query) {
            $query->where('type', cons('promo.content_type.rebate'));
        });
    }

    /**
     * 以编号或名称检索
     * @param $query
     * @param $numberName
     * @return mixed
     */
    public function scopeOfNumberName($query,$numberName)
    {
        $field = is_numeric($numberName) ? 'id' : 'name';
        return $query->where($field, 'LIKE', '%' . $numberName . '%');
    }
}
