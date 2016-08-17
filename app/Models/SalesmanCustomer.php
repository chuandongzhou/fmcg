<?php

namespace App\Models;


class SalesmanCustomer extends Model
{
    protected $table = 'salesman_customer';

    protected $fillable = [
        'number',
        'name',
        'account',
        'shop_id', //平台id
        'letter',
        'contact',
        'contact_information',
        'business_area',
        'business_address_lng',
        'business_address_lat',
        'shipping_address_lng',
        'shipping_address_lat',
        'display_fee',
        'salesman_id',
        'business_address',
        'shipping_address'
    ];

    protected $hidden = ['shop'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->businessAddress()->delete();
            $model->shippingAddress()->delete();
        });
    }

    /**
     * 关联业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('App\Models\Salesman');
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
     * 营业地址
     *
     * @return mixed
     */
    public function businessAddress()
    {
        return $this->morphOne('App\Models\AddressData', 'addressable')->where('type',
            cons('salesman.customer.address_type.business'));
    }


    /**
     * 收货地址
     *
     * @return mixed
     */
    public function shippingAddress()
    {
        return $this->morphOne('App\Models\AddressData', 'addressable')->where('type',
            cons('salesman.customer.address_type.shipping'));
    }

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
     * 关联已销售商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'salesman_customer_sale_goods');
    }

    /**
     * 按名字搜索
     *
     * @param $query
     * @param $name
     * @return mixed
     */
    public function scopeOfName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'LIKE', '%' . $name . '%');
        }
    }

    /**
     * 按业务员搜索
     *
     * @param $query
     * @param $salesmanId
     * @return mixed
     */
    public function scopeOfSalesman($query, $salesmanId)
    {
        if ($salesmanId) {
            return $query->where('salesman_id', $salesmanId);
        }
    }

    /**
     * 设置营业地址
     *
     * @param $address
     * @return bool
     */
    public function setBusinessAddressAttribute($address)
    {
        $relate = $this->businessAddress();
        $relate->delete();
        $address['type'] = cons('salesman.customer.address_type.business');
        if ($this->exists) {
            $relate->create($address);
        } else {
            static::created(function ($model) use ($address) {
                $model->businessAddress()->create($address);
            });
        }
        return true;
    }

    /**
     * 设置营业地址
     *
     * @param $address
     * @return bool
     */
    public function setShippingAddressAttribute($address)
    {
        $relate = $this->shippingAddress();
        $relate->delete();
        $address['type'] = cons('salesman.customer.address_type.shipping');
        if ($this->exists) {
            $relate->create($address);
        } else {
            static::created(function ($model) use ($address) {
                $model->shippingAddress()->create($address);
            });
        }
        return true;
    }

    /**
     * 设置店铺id
     *
     * @param $account
     */
   /* public function setAccountAttribute($account)
    {
        $this->attributes['shop_id'] = ShopService::getShopIdByAccount($account);
    }*/

    /**
     * 获取营业地址
     *
     * @return string
     */
    public function getBusinessAddressNameAttribute()
    {
        $businesAddress = $this->businessAddress;
        return is_null($businesAddress) ? '' : $businesAddress->address_name;
    }

    /**
     * 获取收货地址
     *
     * @return string
     */
    public function getShippingAddressNameAttribute()
    {
        return is_null($this->shippingAddress) ? '' : $this->shippingAddress->address_name;
    }

    /**
     * 获取店铺名
     *
     * @return string
     */
    public function getAccountAttribute()
    {
        return $this->shop_id && $this->shop ? $this->shop->user_name : '';
    }
}
