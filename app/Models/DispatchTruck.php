<?php

namespace App\Models;


use Carbon\Carbon;

class DispatchTruck extends Model
{
    protected $table = 'dispatch_truck';
    protected $fillable = [
        'delivery_truck_id',
        'status',
        'dispatch_time',
        'back_time',
        'remark'
    ];
    public $timestamps = false;


    /**
     * 关联订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * 关联订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function returnOrders()
    {
        return $this->hasMany('App\Models\DispatchTruckReturnOrder');
    }

    /**
     * 关联配送员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deliveryMans()
    {
        return $this->belongsToMany('App\Models\DeliveryMan', 'dispatch_truck_delivery_man', 'dispatch_truck_id',
            'delivery_man_id');
    }


    /**
     * 车销商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function truckSalesGoods()
    {
        return $this->belongsToMany('App\Models\Goods', 'truck_sales_goods', 'dispatch_truck_id')->withPivot(['quantity', 'pieces', 'surplus']);
    }

    /**
     * 关联业务员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo(Salesman::class);
    }

    /**
     * 关联货车
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function truck()
    {
        return $this->belongsTo(DeliveryTruck::class, 'delivery_truck_id');
    }

    /**
     * 操作记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function record()
    {
        return $this->hasMany('App\Models\DispatchTruckRecord', 'dispatch_truck_id');
    }

    /**
     * 以发车单号或者车牌号查询
     *
     * @param $param
     */
    public function scopeCondition($query, $param)
    {
        return $query->where(function ($query) use ($param) {
            $numberLicense = $param['number_license'] ?? '';
            $carbon = new Carbon();

            $startTime = array_get($param, 'start_at') ? $carbon->parse(array_get($param,
                'start_at')) : $carbon->subDay(7)->startOfDay();
            $endTime = array_get($param, 'end_at') ? $carbon->parse(array_get($param, 'end_at')) : $carbon->now();

            if (!$numberLicense) {
                $query->where('dispatch_time', '>=', $startTime->toDateTimeString())->where('dispatch_time',
                    '<=',
                    $endTime->endOfDay()->toDateTimeString());
            }
            if ($delivery_man = array_get($param, 'delivery_man')) {
                $query->whereHas('deliveryMans', function ($deliveryMan) use ($delivery_man) {
                    $deliveryMan->where('id', $delivery_man);
                });
            }
            if (is_numeric($numberLicense)) {
                $query->where('dispatch_truck.id', $numberLicense);
            } else {
                $query->whereHas('truck', function ($truck) use ($numberLicense) {
                    $truck->where('license_plate', 'LIKE', '%' . $numberLicense . '%');
                });
            }
        })->where('dispatch_truck.status', '>=', cons('dispatch_truck.status.delivering'));

    }

    /**
     * 获取订单数量
     *
     * @return mixed
     */
    public function getOrderAmountAttribute()
    {
        return $this->orders->count();
    }

    /**
     * 获取发车单状态名
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return cons()->valueLang('dispatch_truck.status', $this->status);
    }

    /**
     * 是否有退货单
     *
     * @return int
     */
    public function getIsReturnOrderAttribute()
    {
        return $this->returnOrders()->count() ? 1 : 0;
    }
}
