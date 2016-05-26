<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

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
        'spreading_code',
        'status',
        'shop'
    ];
    protected $fillable = [
        'user_name',
        'password',
        'backup_mobile',
        'type',
        'audit_status',
        'spreading_code',
        'last_login_at'
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
        return $this->hasMany('App\Models\ShippingAddress');
    }
    
    /**
     * 订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(){
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

}
