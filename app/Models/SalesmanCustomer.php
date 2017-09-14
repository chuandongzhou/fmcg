<?php

namespace App\Models;


use Carbon\Carbon;

class SalesmanCustomer extends Model
{
    protected $table = 'salesman_customer';

    protected $fillable = [
        'number',
        'name',
        'type',
        'store_type',
        'area_id',
        'account',
        'shop_id', //平台id
        'belong_shop', //所属店铺
        'letter',
        'contact',
        'contact_information',
        'business_area',
        'business_address_lng',
        'business_address_lat',
        'shipping_address_lng',
        'shipping_address_lat',
        'display_type',
        'display_start_month',
        'display_end_month',
        'display_fee',
        'mortgage_goods',
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
            $model->address()->delete();
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
     * 地址
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address()
    {
        return $this->morphOne('App\Models\AddressData', 'addressable');
    }

    /**
     * 关联业务区域
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * 营业地址
     *
     * @return mixed
     */
    public function businessAddress()
    {
        return $this->address()->where('type', cons('salesman.customer.address_type.business'));
    }


    /**
     * 收货地址
     *
     * @return mixed
     */
    public function shippingAddress()
    {
        return $this->address()->where('type', cons('salesman.customer.address_type.shipping'));
    }

    /**
     * 订单列表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\SalesmanVisitOrder');
    }

    /**
     * 关联店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop', 'shop_id');
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
     * 陈列操作记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function displayList()
    {
        return $this->hasMany('App\Models\SalesmanCustomerDisplayList');
    }

    /**
     * 陈列剩余
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function displaySurplus()
    {
        return $this->hasMany('App\Models\SalesmanCustomerDisplaySurplus');
    }


    /**
     * 陈列费商品
     *
     * @return /Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mortgageGoods()
    {
        return $this->belongsToMany('App\Models\MortgageGoods', 'salesman_customer_mortgage')->withPivot([
            'total',
        ]);
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
            return $query->where('salesman_customer.name', 'LIKE', '%' . $name . '%');
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
     * 查找剩余陈列记录
     *
     * @param $query
     * @param $month
     * @param int $mortgageGoodsId
     * @return mixed
     */
    public function scopeOfSurplusDisplay($query, $month, $mortgageGoodsId = 0)
    {
        if (is_array($mortgageGoodsId)) {
            return $this->displayList()
                ->where(['month' => $month])
                ->whereIn('mortgage_goods_id', $mortgageGoodsId)
                ->orderBy('id', 'desc');
        } else {
            return $this->displayList()->where([
                'month' => $month,
                'mortgage_goods_id' => $mortgageGoodsId
            ])->orderBy('id', 'desc');
        }
    }

    /**
     * 设置陈列商品
     *
     * @param $mortgageGoods
     * @return array
     */
    public function setMortgageGoodsAttribute($mortgageGoods)
    {
        $result = [];
        foreach ($mortgageGoods as $id => $num) {
            $result[$id] = [
                'total' => $num,
                'created_at' => Carbon::now(),
            ];
        }
        if ($this->exists) {
            $this->mortgageGoods()->sync($result);
        } else {
            static::created(function ($customer) use ($result) {
                $customer->mortgageGoods()->sync($result);
            });
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
     * 获取平台账号
     *
     * @return string
     */
    public function getAccountAttribute()
    {
        return $this->shop_id && $this->shop ? $this->shop->user_name : '';
    }


    /**
     * 获取客户类型名
     *
     * @return mixed
     */
    public function getTypeNameAttribute()
    {
        return array_search($this->type, cons('user.type'));
    }

    /**
     * 获取业务员名
     *
     * @return string
     */
    public function getSalesmanNameAttribute()
    {
        if (!$this->salesman) {
            return '';
        }
        $user = auth()->user();
        if ($user->type == cons('user.type.supplier')) {
            if ($this->salesman->shop_id <> $user->shop_id) {
                return '';
            }
        }
        return $this->salesman->name;

    }

    /**
     * 获取所属业务区域名
     *
     * @return string
     */
    public function getAreaNameAttribute()
    {
        return $this->area ? $this->area->name : '';
    }

    /**
     * 获取商店类型名称
     *
     * @return string
     */
    public function getStoreTypeNameAttribute()
    {
        return $this->store_type ? cons()->valueLang('salesman.customer.store_type', $this->store_type) : '未指定';
    }

}
