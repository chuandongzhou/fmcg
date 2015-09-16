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
    protected $hidden = ['password', 'remember_token', 'balance', 'spreading_code', 'status'];
    protected $fillable = [
        'user_name',
        'password',
        'type',
        'spreading_code'
    ];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($user) {
            //TODO 删除其它
            $user->carts()->delete();
            $user->likes()->delete();
            $user->shops()->delete();
            $user->userBanks()->delete();
            $user->shippingAddress()->delete();
        });
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
     * 店铺收藏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likeShops()
    {
        return $this->belongsToMany('App\Models\Shop', 'like_shop', 'user_id', 'shop_id');
    }

    /**
     * 商家收藏
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likeGoods()
    {
        return $this->belongsToMany('App\Models\Shop', 'like_goods', 'user_id', 'goods_id');
    }
}
