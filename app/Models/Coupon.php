<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\carbon;
class Coupon extends Model
{
    use softDeletes;

    protected $table = 'coupon';

    protected $fillable = [
        'full',
        'discount',
        'stock',
        'total',
        'start_at',
        'end_at'
    ];

    // protected $dates = ['start_at', 'end_at', 'deleted_at'];

    protected $appends = ['diff_time'];

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
     * 判断优惠券现在是否可用
     *
     * @param $query
     * @return mixed
     */
    public function scopeTime($query)
    {
        return $query->where('start_at','<',Carbon::now())->where('end_at','>',Carbon::now());
    }

    /**
     * 查询可用优惠券
     *
     * @param $query
     * @param $shopId
     * @param $sumPrice
     * @return mixed
     */
    public function scopeOfUseful($query, $shopId, $sumPrice)
    {
        return $query->where('shop_id', $shopId)->where('full', '<=', $sumPrice)->where(function ($query) {
            $nowDate = (new Carbon())->toDateString();
            $query->where('start_at', '<=', $nowDate)->where('end_at', '>', $nowDate);
        });
    }

    /**
     * 用户是否可领取
     *
     * @return bool
     */
    public function getCanReceiveAttribute()
    {
        $nowDate = (new Carbon)->toDateString();
        if ($this->stock <= 0 || $this->end_at < $nowDate) {
            return false;
        }

        $shopUser = $this->shop->user;
        $user = auth()->user();

        return !$user->coupons()->find($this->id) && $shopUser->id != $user->id && $shopUser->type > $user->type;
    }


    /**
     * 获取当前广告状态
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        $nowDate = (new Carbon)->toDateString();
        if (!is_null($this->deleted_at)) {
            return '已删除';
        }
        if ($this->start_at && $this->start_at > $nowDate) {
            return '未开始';
        }

        $endAt = $this->end_at;
        if (is_null($endAt)) {
            return '永久';
        }

        if ($endAt < $nowDate) {
            return '已结束';
        }

        return '优惠中';
    }

    /**
     * 优惠券离结束时间
     *
     * @return string
     */
    public function getDiffTimeAttribute()
    {
        $endAt = new Carbon($this->end_at);
        $diffDay = $endAt->diffInDays();
        if ($diffDay <= 1) {
            $diffHour = $endAt->diffInHours();
            if ($diffHour < 1) {
                $diffMinutes = $endAt->diffInMinutes();
                return $diffMinutes . '分';
            } else {
                return $diffHour . '时';
            }

        } elseif ($diffDay <= 15) {
            return $diffDay . '天';
        }
        return '';
    }


}
