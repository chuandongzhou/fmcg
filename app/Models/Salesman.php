<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Salesman extends Model implements AuthenticatableContract
{
    use SoftDeletes;
    use Authenticatable;
    protected $table = 'salesman';

    protected $fillable = [
        'account',
        'password',
        'shop_id',
        'name',
        'avatar',
        'contact_information',
        'last_login_ip',
        'last_login_time',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
        'deleted_at',
        'last_login_ip',
        'last_login_time'
    ];

    /**
     * 模型启动事件
     */
    /*public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->customers()->delete();
        });
    }*/

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

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
     * 关联客户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany('App\Models\SalesmanCustomer');
    }

    /**
     * 关联拜访表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visits()
    {
        return $this->hasMany('App\Models\SalesmanVisit');
    }

    /**
     * 关联订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\SalesmanVisitOrder');
    }

    /**
     * 订货单
     *
     * @return mixed
     */
    public function orderForms()
    {
        return $this->orders()->where('type', cons('salesman.order.type.order'));
    }

    /**
     * 退货单
     *
     * @return mixed
     */
    public function returnOrders()
    {
        return $this->orders()->where('type', cons('salesman.order.type.return_order'));
    }

    /**
     * 设置头像
     *
     * @param mixed $file
     */
    public function setAvatarAttribute($file)
    {

        if (is_string($file)) {
            $file = config('path.upload_temp') . $file;
        } else {
            $result = $this->convertToFile($file, null, false);
            $file = $result ? $result['path'] : null;
            $file = config('path.upload_temp') . $file;
        }

        try {
            $image = \Image::make($file);
        } catch (\Exception $e) {
            return;
        }
        $sizes = array_keys(cons('salesman.avatar'));
        $avatarPath = config('path.upload_salesman_avatar');

        rsort($sizes);
        if ($this->exists) {
            $uid = array_get($this->attributes, $this->primaryKey);
            $pathName = implode('/', divide_uid($uid, '_{size}.jpg'));

            // 创建目录
            $folder = $avatarPath . dirname($pathName);
            if (!is_dir($folder)) {
                mkdir($folder, 0770, true);
            }

            foreach ($sizes as $size) {
                $avatarFile = str_replace('{size}', $size, $pathName);
                $image->widen($size)->save($avatarPath . $avatarFile);
            }
        } else {
            static::created(function ($model) use ($file, $avatarPath, $sizes, $image) {
                $uid = array_get($model->attributes, $model->primaryKey);
                $pathName = implode('/', divide_uid($uid, '_{size}.jpg'));

                // 创建目录
                $folder = $avatarPath . dirname($pathName);
                if (!is_dir($folder)) {
                    mkdir($folder, 0770, true);
                }

                foreach ($sizes as $size) {
                    $avatarFile = str_replace('{size}', $size, $pathName);
                    $image->widen($size)->save($avatarPath . $avatarFile);
                }
            });
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
     * Get user avatar url.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return salesman_avatar_url(array_get($this->attributes, $this->primaryKey), 64);
    }

    /**
     * 获取店铺类型
     *
     * @return mixed
     */
    public function getShopTypeAttribute()
    {
        return $this->shop->user_type;
    }

    /**
     * 获取店铺地址
     *
     * @return mixed
     */
    public function getShopAddressAttribute()
    {
        return $this->shop()->first()->address;
    }

    /**
     * 按名字检索
     *
     * @param $query
     * @param $name
     * @param bool $includeAccount
     * @return mixed
     */
    public function scopeOfName($query, $name, $includeAccount = false)
    {
        if ($name) {
            return $includeAccount ? $query->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', '%' . $name . '%')
                    ->orWhere('account', 'LIKE', '%' . $name . '%');
            }) : $query->where('name', 'LIKE', '%' . $name . '%');
        }
        return $query;
    }

    /**
     * 按参数检索
     *
     * @param $query
     * @param $options
     * @return mixed
     */
    public function scopeOfOptions($query, $options)
    {
        return $query->where(array_filter($options));
    }

}
