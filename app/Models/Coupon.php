<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use softDeletes;

    protected $table = 'coupon';

    protected $fillable = [
        'full',
        'discount',
        'stock',
        'start_at',
        'end_at'
    ];

    protected $dates = ['start_at', 'end_at', 'deleted_at'];

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsToMany('App\Models\User', 'user_coupon');
    }

    /**
     * 关联订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * 获取当前广告状态
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        if (!is_null($this->deleted_at)) {
            return '已删除';
        }
        if ($this->start_at && $this->start_at->isFuture()) {
            return '未开始';
        }

        $endAt = $this->end_at;
        if (is_null($endAt)) {
            return '永久';
        }

        if ($endAt->isPast()) {
            return '已结束';
        }

        return '优惠中';
    }

}
