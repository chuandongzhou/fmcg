<?php

namespace App\Models;


class SalesmanVisit extends Model
{
    protected $table = 'salesman_visit';

    protected $fillable = [
        'salesman_customer_id',
        'x_lng',
        'y_lat',
        'address'
    ];

    protected $hidden = ['updated_at'];

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
     * 关联客户表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesmanCustomer()
    {
        return $this->belongsTo('App\Models\SalesmanCustomer');
    }

    /**
     * 关联订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orders()
    {
        return $this->hasMany('App\Models\SalesmanVisitOrder');
    }

    /**
     * 关联拜访商品记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsRecord()
    {
        return $this->hasMany('App\Models\SalesmanVisitGoodsRecord');
    }

    /**
     * 按拜访时间查询
     *
     * @param $query
     * @param $beginTime
     * @param $endTime
     * @return mixed
     */
    public function scopeOfTime($query, $beginTime = null, $endTime = null)
    {
        if ($beginTime) {
            $query = $query->where('created_at', '>=', $beginTime);
        }
        if ($endTime) {
            $query = $query->where('created_at', '<=', $endTime);
        }
        return $query;
    }

    /**
     * 排序
     *
     * @param $query
     * @param string $field
     * @param string $sort
     * @return mixed
     */
    public function scopeOfSort($query, $field = 'id', $sort = 'DESC')
    {
        return $query->orderBy($field, $sort);
    }

    /**
     * 获取订单详情
     *
     * @return array
     */
    public function getOrderDetailAttribute()
    {
        $orders = $this->orders;
        $types = cons('salesman.order.type');

        $order = $orders->where('type', $types['order'])->first();
        $returnOrder = $orders->where('type', $types['return_order'])->first();

        $orderAmount = $order ? $order->amount : 0;
        $returnOrderAmount = $returnOrder ? $returnOrder->amount : 0;

        return [
            'order_amount' => $orderAmount,
            'return_order_amount' => $returnOrderAmount
        ];
    }

}
