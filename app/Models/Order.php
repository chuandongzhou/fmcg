<?php

namespace App\Models;


use Carbon\Carbon;

class   Order extends Model
{
    protected $table = 'order';
    protected $fillable = [
        'pid',
        'price',
        'pay_type',
        'pay_way',
        //'receive_mode',
        'remark',
        'pay_status',
        'status',
        'shipping_address_id',
        'delivery_man_id',
        'coupon_id',
        'user_id',
        'shop_id',
        'paid_at',
        'confirm_at',
        'send_at',
        'refund_at',
        'confirmed_at',
        'finished_at',
        'is_cancel',
        'cancel_by',
        'cancel_at'
    ];

    protected $appends = [
        'status_name',
        'payment_type',
        'step_num',
        'can_cancel',
        'can_confirm',
        'can_send',
        'can_confirm_collections',
        'can_export',
        'can_payment',
        'can_confirm_arrived'
    ];

    protected $hidden = [];

    /**
     * 模型启动事件
     */
    public static function boot()
    {
        parent::boot();

        // 注册删除事件
        static::deleted(function ($model) {
            // 删除所有关联文件
            $model->orderGoods()->delete();
            $model->orderChangeRecode()->delete();
            $model->orderRefund()->delete();
            $model->shippingAddress()->delete();
        });
    }

    /**
     * 该订单下所有商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany('App\Models\OrderGoods');
    }

    /**
     * 订单修改记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderChangeRecode()
    {
        return $this->hasMany('App\Models\OrderChangeRecode');
    }

    /**
     * 订单退款信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderRefund()
    {
        return $this->hasOne('App\Models\OrderRefund');
    }

    /**
     * 收货人信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress()
    {
        return $this->belongsTo('App\Models\ShippingAddressSnapshot');
    }

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 店铺信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }


    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'order_goods', 'order_id',
            'goods_id')->withTrashed()->withPivot('id', 'price',
            'num', 'total_price', 'pieces');
    }

    /**
     * 关联交易信息,仅在线支付成功后有对应信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function systemTradeInfo()
    {
        return $this->hasOne('App\Models\SystemTradeInfo', 'order_id', 'id');
    }

    /**
     * 订单配送人员信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryMan()
    {
        return $this->belongsTo('App\Models\DeliveryMan')->withTrashed();
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo('App\Models\Coupon')->withTrashed();
    }

    /**
     * 支付形式
     *
     * @return mixed
     */
    public function getPaymentTypeAttribute()
    {
        $type = $this->pay_type;

        return cons()->valueLang('pay_type', $type);
    }

    /**
     * 货到付款支付方式
     *
     * @return string
     */
    public function getPayWayLangAttribute()
    {
        $payWay = $this->attributes['pay_way'];

        return cons()->valueLang('pay_way.cod', $payWay);
    }

    /**
     * 订单状态显示
     *
     * @return mixed
     */
    public function getStatusNameAttribute()
    {
        $status = $this->attributes['status'];
        $payStatus = $this->attributes['pay_status'];
        $payType = $this->attributes['pay_type'];
        $isCancel = $this->attributes['is_cancel'];
        $payStatusArr = cons('order.pay_status');
        $statusConf = cons('order.status');
        // 未确认 =未支付  =退款中  =已退款 未发货 已发货 完成          在线 / 货到付款


        //未提货   完成                                              自提

        //取消
        if ($isCancel) {
            return cons()->lang('order.is_cancel.on');
        }

        // 未确认  已完成
        if ($status == $statusConf['non_confirm'] || $status == $statusConf['finished']) {
            return cons()->valueLang('order.status', $status);
        }


        if ($payType == cons('pay_type.pick_up')) {
            //自提
            return $status == $statusConf['finished'] ? '已完成' : '未提货';

            /*if (!$payStatus || $payStatus == $payStatusArr['refund'] || $payStatus == $payStatusArr['refund_success']) {//显示未支付
                return cons()->valueLang('order.pay_status', $payStatus);
            }
            //已支付
            if ($payStatus == $payStatusArr['payment_success'] && $status < $statusConf['send']) {
                return cons()->valueLang('order.pay_status', $payStatus) . ',' . cons()->valueLang('order.status',
                    $status);
            }*/
        } else {
            //在线支付和货到付款
            if ($payStatus == $payStatusArr['refund'] || $payStatus == $payStatusArr['refund_success']) {
                return cons()->valueLang('order.pay_status', $payStatus);
            }
            return cons()->valueLang('order.pay_status', $payStatus) . ',' . cons()->valueLang('order.status', $status);

        }
    }

    /**
     * 进度条显示
     *
     * @return mixed
     */
    public function getStepNumAttribute()
    {
        $payType = $this->attributes['pay_type'];//支付方式
        $payStatus = $this->attributes['pay_status'];//支付状态
        $status = $this->attributes['status'];//订单状态
        if ($payType == cons('pay_type.online')) {//在线支付
            return $payStatus ? ($status == 0 ? 2 : $status + 1) : $status;
        }
        //货到付款
        if ($payStatus) {
            return $status + 1;
        }
        if ($status <= cons('order.status.send')) {
            return $status;
        }
    }

    /**
     * 获取退款原因
     *
     * @return string
     */
    public function getRefundReasonAttribute()
    {
        $refund = $this->orderRefund;
        return $refund ? $refund->reason : '';
    }

    /**
     * 是否可取消
     *
     * @return bool
     */
    public function getCanCancelAttribute()
    {
        $orderConf = cons('order');
        return $this->attributes['status'] == $orderConf['status']['non_confirm']
        && $this->attributes['pay_status'] == $orderConf['pay_status']['non_payment']
        && $this->attributes['is_cancel'] == $orderConf['is_cancel']['off']/* && ($this->attributes['user_id'] == auth()->id() || $this->attributes['shop_id'] == auth()->user()->shop()->pluck('id'))*/
            ;
    }

    /**
     * 是否可确认订单(卖家)
     *
     * @return bool
     */
    public function getCanConfirmAttribute()
    {
        return $this->attributes['status'] == cons('order.status.non_confirm')
        && $this->attributes['is_cancel'] == cons('order.is_cancel.off');
    }

    /**
     * 是否可发货(卖家)
     *
     * @return bool
     */
    public function getCanSendAttribute()
    {
        $payType = $this->pay_type;
        $status = $this->status;
        $payTypeConf = cons('pay_type');

        $result = false;
        if ($payType == $payTypeConf['online']) {
            $result = ($this->pay_status == cons('order.pay_status.payment_success')) && ($status == cons('order.status.non_send'));
        } elseif ($payType == $payTypeConf['cod']) {
            $result = $status == cons('order.status.non_send');
        }
        return $result && $this->is_cancel == cons('order.is_cancel.off');

        /* return ($this->pay_type == cons('pay_type.online') ? $this->pay_status == cons('order.pay_status.payment_success') : true)
         && $this->attributes['status'] == cons('order.status.non_send') && $this->attributes['is_cancel'] == cons('order.is_cancel.off');*/
    }

    /**
     * 是否可退款
     *
     * @return bool
     */
    public function getCanRefundAttribute()
    {
        return $this->attributes['pay_status'] == cons('order.pay_status.payment_success')
        && $this->attributes['status'] == cons('order.status.non_confirm');
    }

    /**
     * 是否可收款(卖家)
     *
     * @return bool
     */
    public function getCanConfirmCollectionsAttribute()
    {
        $payType = $this->pay_type;
        $status = $this->status;
        $payTypeConf = cons('pay_type');
        $statusConf = cons('order.status');

        return ($payType == $payTypeConf['cod'] && $status == $statusConf['send'])
        || ($payType == $payTypeConf['pick_up'] && ($status > $statusConf['non_confirm'] && $status < $statusConf['finished']));
    }

    /**
     * 是否可导出(卖家)
     *
     * @return bool
     */
    public function getCanExportAttribute()
    {
        return $this->attributes['status'] == cons('order.status.send') || $this->attributes['status'] == cons('order.status.non_send');
    }

    /**
     * 是否可付款(
     *
     * @return bool
     */
    public function getCanPaymentAttribute()
    {
        $status = $this->status;
        $payStatus = $this->pay_status;
        $statusArr = cons('order.status');
        $payStatusArr = cons('order.pay_status');
        return $this->attributes['is_cancel'] == cons('order.is_cancel.off')
        && $payStatus == $payStatusArr['non_payment']
        && ($status >= $statusArr['non_send'] && $status < $statusArr['finished'])
        && $this->attributes['pay_type'] != cons('pay_type.pick_up');
        /*
                return
                    ($this->attributes['pay_type'] == cons('pay_type.online') || ($this->attributes['pay_type'] == cons('pay_type.cod')) && $this->attributes['status'] == cons('order.status.send'))
                    && $this->attributes['pay_status'] == cons('order.pay_status.non_payment')
                    && $this->attributes['status'] == cons('order.status.non_send')
                    && $this->attributes['is_cancel'] == cons('order.is_cancel.off');*/
    }

    /**
     * 是否可确认收货(买家在线支付)
     *
     * @return bool
     */
    public function getCanConfirmArrivedAttribute()
    {
        return $this->attributes['pay_type'] == cons('pay_type.online')
        && $this->attributes['status'] == cons('order.status.send')
        && $this->attributes['pay_status'] == cons('order.pay_status.payment_success');
    }


    /**
     * 是否可修改单价
     *
     * @return bool
     */
    public function getCanChangePriceAttribute()
    {
        $orderConf = cons('order');
        return $this->attributes['pay_type'] == cons('pay_type.online')
            ? $this->attributes['pay_status'] == $orderConf['pay_status']['non_payment']
            : $this->attributes['status'] < $orderConf['status']['finished'];
    }

    /**
     * 根据不同角色获取单位
     *
     * @return mixed
     */
    public function getPiecesAttribute()
    {
        $userType = $this->user->type;
        $piece = $userType == cons('user.type.wholesaler') ? $this->pieces_wholesaler : $this->pieces_retailer;
        return cons()->valueLang('goods.pieces', $piece);
    }

    /**
     * 获取订单优惠后价格
     *
     * @return mixed|string
     */
    public function getAfterRebatesPriceAttribute()
    {
        $price = $this->price;
        return ($this->coupon_id && $this->coupon) ? (bcsub($price, $this->coupon->discount, 2)) : $price;
    }

    /**
     * 根据买家名字查询订单及买家信息--getSearch()
     *
     * @param $query
     * @param $shopName
     * @return mixed
     */
    public function scopeOfUserShopName($query, $shopName)
    {
        return $query->whereHas('user.shop', function ($query) use ($shopName) {

            $query->where('name', 'like', '%' . $shopName . '%');
        });
    }

    /**
     * 根据卖家名字查询订单及卖家信息--getSearch()
     *
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeOfSellerShopName($query, $search)
    {
        return $query->whereHas('shop', function ($query) use ($search) {

            $query->where('name', 'like', '%' . $search . '%');


        });
    }

    /**
     * 购买订单条件
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeOfBuy($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('is_cancel', cons('order.is_cancel.off'))
            ->orderBy('id', 'desc');
    }

    /**
     * 销售订单条件
     *
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeOfSell($query, $userId)
    {
        return $query->whereHas('shop.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->where('is_cancel', cons('order.is_cancel.off'))->orderBy('id', 'desc');
    }

//    /**
//     * 通过shop_id查询卖家
//     *
//     * @param $query
//     * @param $userId
//     * @return mixed
//     */
//    public function scopeBySellerId($query, $userId)
//    {
//        return $query->whereHas('shop.user', function ($query) use ($userId) {
//            $query->where('id', $userId);
//        });
//
//    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeExceptNonPayment($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                    cons('order.pay_status.payment_success'));
            })->orWhere('pay_type', cons('pay_type.cod'));
        });
    }

    /**
     * 未取消条件
     *
     * @param $query
     * @return mixed
     */
    public function scopeNonCancel($query)
    {
        return $query->where('is_cancel', cons('order.is_cancel.off'));
    }

    /**
     * 待付款
     *
     * @param $query
     * @return mixed
     */
    public function scopeNonPayment($query)
    {
        return $query->where(function ($query) {
            $statusArr = cons('order.status');
            $query->where('status', '>=', $statusArr['non_send'])->where('status', '<', $statusArr['finished']);
        })->where('pay_status', cons('order.pay_status.non_payment'))->where('pay_type', '<>',
            cons('pay_type.pick_up'));
    }

    /**
     * 待确认
     *
     * @param $query
     * @return mixed
     */
    public function scopeWaitConfirm($query)
    {
        return $query->where('status', cons('order.status.non_confirm'))->NonCancel();
    }

    /**
     * 待收款,货到付款
     *
     * @param $query
     * @return mixed
     */
    public function scopeGetPayment($query)
    {
        return $query->where('pay_type', cons('pay_type.cod'))->where('status', cons('order.status.send'));
    }

    /**
     * 未发货
     *
     * @param $query
     * @return mixed
     */
    public function scopeNonSend($query)
    {
        return $query->where(function ($query) {
            $query->where([
                'pay_type' => cons('pay_type.online'),
                'pay_status' => cons('order.pay_status.payment_success')
            ])->orWhere('pay_type', cons('pay_type.cod'));
        })->where('status', cons('order.status.non_send'))->nonCancel();
    }


    /**
     * 待收货,分在线支付和货到付款来讨论
     * 在线支付,在支付完成后,确认收货前,为待收货状态
     * 货到付款,在卖家发货后,确认收到货前,为待收货状态
     *
     * @param $query
     * @return mixed
     */
    public function scopeNonArrived($query)
    {
        return $query->where(['status' => cons('order.status.send')]);
    }

    /**
     * 根据select发送过来的实际参数生成查询语句
     *
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeOfSelectOptions($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            if ($search['pay_type']) {
                $query->where('pay_type', $search['pay_type']);
            }
            if ($search['status']) {
                if ($search['status'] == key(cons('order.pay_status'))) {
                    //查询未付款
                    $query->where('pay_status', cons('order.pay_status.non_payment'))
                        ->where('pay_type', '<>', cons('pay_type.pick_up'));
                } elseif ($search['status'] == 'non_send') {//未发货
                    $query->where(function ($query) {
                        $query->where([
                            'pay_type' => cons('pay_type.online'),
                            'pay_status' => cons('order.pay_status.payment_success')
                        ])->orWhere(['pay_type' => cons('pay_type.cod')]);
                    })->where('status', cons('order.status.non_send'));
                } else {
                    $query->where('status', cons('order.status.' . $search['status']));
                }
            }
            if ($search['start_at']) {
                $query->where('created_at', '>=', $search['start_at']);
            }
            if ($search['end_at']) {
                $endAt = (new Carbon($search['end_at']))->endOfDay();
                $query->where('created_at', '<=', $endAt);
            }
        })->nonCancel();
    }

    /**
     * 在线支付,支付成功的订单条件
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfPaySuccess($query)
    {
        return $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
            cons('order.pay_status.payment_success'));
    }

    /**
     * 货到付款,已发货或已完成的订单条件
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfHasSend($query)
    {
        return $query->where('pay_type', cons('pay_type.cod'))->whereIn('status',
            [cons('order.status.send'), cons('order.status.finished')]);
    }

    /**
     * 自提订单
     *
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeOfPickUp($query, $type = null)
    {
        $type = $type ?: cons('order.status.finished');

        return $query->where('pay_type', cons('pay_type.pick_up'))->where('status', $type);
    }
}
