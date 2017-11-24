<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class WarehouseKeeper extends Model implements AuthenticatableContract
{
    use SoftDeletes;
    use Authenticatable;
    protected $table = 'warehouse_keeper';
    protected $fillable = [
        'name',
        'phone',
        'status',
        'shop_id',
        'account',
        'password',
        'shop_id',
        'remember_token',
        'status',
        'name'
    ];

    protected $hidden = ['password'];

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 设置密码
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = bcrypt($password);
        }
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
     *获取状态名
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return cons()->valueLang('status', $this->status);
    }

}
