<?php

namespace App\Models;


class SalesmanVisit extends Model
{
    protected $table = 'salesman_visit';

    protected $fillable = ['salesman_customer_id'];

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
    public function scopeOfTime($query, $beginTime, $endTime)
    {
        if ($beginTime) {
            $query = $query->where('created_at', '>=', $beginTime);
        }
        if ($endTime) {
            $query = $query->where('created_at', '<=', $endTime);
        }
        return $query;
    }


}
