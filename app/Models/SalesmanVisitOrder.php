<?php

namespace App\Models;


class SalesmanVisitOrder extends Model
{
    protected $table = 'salesman_visit_order';

    protected $fillable = [
        'amount',
        'display_fee',
        'is_synced',
        'status',
        'type',
        'salesman_visit_id',
        'salesman_customer_id'
    ];

    protected $hidden = [
        'updated_at',
        'order_goods'
    ];

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\SalesmanVisitOrderGoods');
    }

    /**
     * 关联抵费商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mortgageGoods()
    {
        return $this->belongsToMany('App\Models\MortgageGoods',
            'salesman_visit_order_mortgage_goods')->withPivot('num');
    }

    /**
     * 关联访问表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salesmanVisit()
    {
        return $this->belongsTo('App\Models\SalesmanVisit');
    }

    /**
     * 关联客户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesmanCustomer()
    {
        return $this->belongsTo('App\Models\SalesmanCustomer');
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
     * 未处理订单
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfUntreated($query)
    {
        return $query->where('status', 0);
    }

    /**
     * 获取客户名
     *
     * @return string
     */
    public function getCustomerNameAttribute()
    {
        return $this->salesmanCustomer ? $this->salesmanCustomer->name : '';
    }

    /**
     * 获取联系人
     *
     * @return string
     */
    public function getCustomerContactAttribute()
    {
        return $this->salesmanCustomer ? $this->salesmanCustomer->contact : '';
    }

    /**
     * 获取业务员名字
     *
     * @return string
     */
    public function getSalesmanNameAttribute()
    {
        return $this->salesman ? $this->salesman->name : '';
    }

    /**
     * 获取订单收货地址
     *
     * @return string
     */
    public function getShippingAddressAttribute()
    {
        $salesmanCustomer = $this->salesmanCustomer;
        if (is_null($salesmanCustomer)) {
            return '';
        }
        return $salesmanCustomer->shipping_address_name;
    }

    /**
     *  是否可导出
     *
     * @return bool
     */
    public function getCanExportAttribute()
    {
        return $this->status == cons('salesman.order.status.passed');
    }

    /**
     * 是否可通过
     *
     * @return bool
     */
    public function getCanPassAttribute()
    {
        return !$this->can_export;
    }

    /**
     * 订单是否可同步
     *
     * @return bool
     */
    public function getCanSyncAttribute()
    {
        return $this->customer_shop_id && $this->is_synced != cons('salesman.order.is_synced.synced');
    }

    /**
     * 获取订单客户平台店铺id
     *
     * @return mixed
     */
    public function getCustomerShopIdAttribute()
    {
        return $this->salesmanCustomer->shop_id;
    }

    /**
     * 获取订单客户平台店铺用户id
     *
     * @return mixed
     */
    public function getCustomerUserIdAttribute()
    {
        return $this->salesmanCustomer->shop ? $this->salesmanCustomer->shop->user_id : 0;
    }
}
