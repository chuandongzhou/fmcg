<?php

namespace App\Models;

use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
        'balance',
        'status',
        'shop'
    ];
    protected $fillable = [
        'user_name',
        'password',
        'backup_mobile',
        'type',
        'deposit',
        'audit_status',
        'expire_at',
        'last_login_at'
    ];

    protected $dates = ['expire_at'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($user) {
            $user->carts()->delete();
            $user->likeShops()->delete();
            $user->likeGoods()->delete();
            $user->shop()->delete();
            $user->userBanks()->delete();
            $user->shippingAddress()->delete();
        });
        /*
                //创建事件
                static::creating(function ($model) {
                    //自动免费3个月
                    $model->attributes['expire_at'] = Carbon::now()->addMonth(3);
                });*/
    }

    /**
     * 购物车ﳵ
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany('App\Models\Cart');
    }

    /**
     * 店铺收藏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likeShops()
    {
        return $this->belongsToMany('App\Models\Shop', 'like_shop', 'user_id', 'shop_id');
    }

    /**
     * 商品收藏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likeGoods()
    {
        return $this->belongsToMany('App\Models\Goods', 'like_goods', 'user_id', 'goods_id');
    }

    /**
     * 店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shop()
    {
        return $this->hasOne('App\Models\Shop');
    }

    /**
     * 银行
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userBanks()
    {
        return $this->hasMany('App\Models\UserBank');
    }

    /**
     * 收货地址表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingAddress()
    {
        return $this->hasMany('App\Models\ShippingAddress')->orderBy('is_default', 'desc');
    }

    /**
     * 订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * 提现
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdraw()
    {
        return $this->hasMany('App\Models\Withdraw');
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function coupons()
    {
        return $this->belongsToMany('App\Models\Coupon', 'user_coupon')->withPivot([
            'used_at',
            'received_at'
        ])->withTrashed();
    }

    /**
     * 续费记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function renews()
    {
        return $this->hasMany(RenewRecord::class);
    }

    /**
     * 按名字检索
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeOfName($query, $name)
    {
        if ($name) {
            return $query->where('user_name', 'Like', '%' . $name . '%');
        }
    }

    /**
     * 是否已缴纳保证金检索
     *
     * @param $query
     * @param $depositPay
     * @return mixed
     */
    public function scopeOfDepositPay($query, $depositPay)
    {
        if ($depositPay) {
            return $query->where('deposit', '>', 0);
        } elseif ($depositPay === '0') {
            return $query->where('deposit', '=', 0);
        }
    }

    /**
     * 密码自动转换
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * 获取用户类型名
     *
     * @return mixed
     */
    public function getTypeNameAttribute()
    {
        return array_search($this->type, cons('user.type'));
    }

    /**
     * 获取店铺ID
     *
     * @return int
     */
    public function getShopIdAttribute()
    {
        $userService = new UserService(true);

        if ($shopId = $userService->getShopDetail($this->id, 'id')) {
            return $shopId;
        }
        return $userService->setShopDetail($this);
    }

    /**
     * 获取店铺名
     *
     * @return int|mixed|string
     */
    public function getShopNameAttribute()
    {
        $userService = new UserService(true);

        if ($shopName = $userService->getShopDetail($this->id, 'name')) {
            return $shopName;
        }
        return $userService->setShopDetail($this, 'name');
    }

    /**
     * 获取model名
     *
     * @return string
     */
    public function getModelNameAttribute()
    {
        return '账户' . $this->attributes['user_name'];
    }

    /**
     * 判断是否到期
     *
     * @return mixed
     */
    public function getIsExpireAttribute()
    {
        return false && $this->expire_at->isPast();
    }


}
