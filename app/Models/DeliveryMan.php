<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class DeliveryMan extends Model implements AuthenticatableContract
{
    use SoftDeletes;
    use Authenticatable;

    protected $table = 'delivery_man';
    protected $fillable = [
        'user_name',
        'password',
        'pos_sign',
        'name',
        'phone',
        'shop_id',
        'status',
        'last_login_at',
        'expire_at'
    ];
    protected $hidden = ['password', 'created_at', 'updated_at', 'deleted_at', 'last_login_at'];

    protected $dates = ['expire_at'];


    /**
     * 模型启动事件
     */
    /*public static function boot()
    {
        parent::boot();

        // 注册创建事件
        static::creating(function ($model) {
            $signService = app('sign');
            $signConfig = cons('sign');
            if ($signService->workerCount() >= $signConfig['max_worker']) {
                $model->attributes['expire_at'] = Carbon::now();
            }
        });
    }*/

    /**
     * 店铺表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * 订单
     *
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_delivery_man');
    }

    /**
     * 密码加密
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = md5($password);
        }
    }

    /**
     * 获取实际过期时间
     *
     * @return mixed
     */
    public function getExpireAttribute()
    {
        return is_null($this->expire_at) ? $this->shop->user->expire_at : $this->expire_at;
    }

    /**
     * 获取店铺名
     *
     * @return string
     */
    public function getShopNameAttribute()
    {
        return $this->shop_id && $this->shop ? $this->shop->name : '';
    }

    /**
     * 是否过期
     *
     * @return mixed
     */
    public function getIsExpireAttribute()
    {
        return false && $this->expire->isPast();
    }

    /**
     * 获取model名
     *
     * @return string
     */
    public function getModelNameAttribute()
    {
        return '司机' . $this->attributes['name'];
    }


}
