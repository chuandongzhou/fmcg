<?php

namespace App\Models;


use Carbon\Carbon;

class SalesmanVisitOrder extends Model
{
    protected $table = 'salesman_visit_order';

    protected $fillable = [
        'id',
        'amount',
        'order_id',
        'status',
        'type',
        'order_remark',
        'display_remark',
        'salesman_id',
        'salesman_visit_id',
        'salesman_customer_id',
        'created_at'
    ];

    protected $hidden = [
        'updated_at',
        'order_goods',
        'display_fee',
        'display_list'
    ];

    protected $appends = ['order_status_name'];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            $model->orderGoods()->delete();
            $model->mortgageGoods()->detach();
            $model->displayList()->delete();
        });
    }

    /**
     * 平台订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

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
            'salesman_customer_display_list')->withTrashed()->withPivot('used', 'month', 'salesman_customer_id');
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
     * 陈列费
     *
     * @return mixed
     */
    public function displayFees()
    {
        return $this->displayList()->where('mortgage_goods_id', 0);
    }


    /**
     * 关联抵费商品(新)
     *
     * @return mixed
     */
    public function newMortgageGoods()
    {
        return $this->belongsToMany('App\Models\MortgageGoods',
            'salesman_customer_display_list')->where('mortgage_goods_id', '>', 0)->withTrashed()->withPivot('month',
            'used', 'salesman_customer_id');
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
     * 订单过滤
     *
     * @param $query
     * @param $data
     * @return mixed
     */
    public function scopeOfData($query, $data)
    {
        if (isset($data['start_date']) && ($startDate = $data['start_date'])) {
            $query = $query->where('created_at', '>=', $startDate);
        }

        if (isset($data['end_date']) && ($endDate = $data['end_date'])) {
            $date = $endDate instanceof Carbon ? $endDate : (new Carbon($endDate))->endOfDay();
            $query = $query->where('created_at', '<', $date);
        }


        $filter = array_filter(array_only($data, ['salesman_id', 'status', 'type']), function ($item) {
            return !is_null($item);
        });
        $query = $query->where($filter);
        if (isset($data['customer']) && !empty($data['customer'])) {
            $customer = $data['customer'];
            if (is_numeric($customer)) {
                return $query->where('id', $customer);
            } else {
                return $query = $query->whereHas('salesmanCustomer', function ($query) use ($customer) {
                    $query->where('name', 'like', '%'.$customer.'%');
                });
            }
        } else {
            return $query;
        }


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
     * 获取客户地址
     *
     * @return string
     */
    public function getBusinessAddressAttribute()
    {
        $salesmanCustomer = $this->salesmanCustomer;
        if (is_null($salesmanCustomer)) {
            return '';
        }
        return $salesmanCustomer->businessAddress;
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
        return $this->status != cons('salesman.order.status.passed')/* && !$this->orderGoods->isEmpty()*/;
    }

    /**
     * 订单是否可同步
     *
     * @return bool
     */
    public function getCanSyncAttribute()
    {
        return $this->order_id == 0 && $this->type == cons('salesman.order.type.order');
    }

    /**
     * 获取订单客户平台店铺id
     *
     * @return mixed
     */
    public function getCustomerShopIdAttribute()
    {
        return $this->salesmanCustomer ? $this->salesmanCustomer->shop_id : 0;
    }

    /**
     * 获取订单客户平台店铺用户id
     *
     * @return mixed
     */
    public function getCustomerUserIdAttribute()
    {
        $salesmanCustomer = $this->salesmanCustomer;
        return $salesmanCustomer->shop ? $salesmanCustomer->shop->user_id : bcmul($salesmanCustomer->id, -1);
    }

    /**
     * 获取订单状态
     *
     * @return string
     */
    public function getOrderStatusNameAttribute()
    {
        return $this->order_id && $this->order ? $this->order->status_name : '未审核';
    }

    /**
     * 获取客户类型
     *
     * @return int
     */
    public function getCustomerTypeAttribute()
    {
        return $this->salesmanCustomer ? $this->salesmanCustomer->type : 1;
    }
}
