<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryMan extends Model
{
    use SoftDeletes;

    protected $table = 'delivery_man';
    protected $fillable = ['user_name', 'password', 'pos_sign', 'name', 'phone', 'shop_id', 'last_login_at'];
    protected $hidden = ['password', 'created_at', 'updated_at', 'deleted_at', 'last_login_at'];

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
}
