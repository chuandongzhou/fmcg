<?php

namespace App\Models;


use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'order';
    protected $fillable = [
        'pid',
        'numbers',
        'price',
        'pay_type',
        'pay_way',
        'remark',
        'pay_status',
        'status',
        'shipping_address_id',
        'coupon_id',
        'display_fee',
        'user_id',
        'shop_id',
        'apply_promo_id',
        'download_count',
        'type',
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
        'can_confirm_arrived',
        //'can_change_price'
    ];

    protected $hidden = ['orderReason'];

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
            $model->orderReason()->delete();
            $model->shippingAddress()->delete();
        });
    }

    /**
     * 业务订单
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function salesmanVisitOrder()
    {
        return $this->hasOne('App\Models\SalesmanVisitOrder');
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
     * 该订单下所有商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applyPromo()
    {
        return $this->belongsTo(PromoApply::class);
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
    public function orderReason()
    {
        return $this->hasMany('App\Models\OrderReason');
    }

    /**
     * 收货人信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
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
            'num', 'total_price', 'pieces', 'type');
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
        return $this->belongsToMany('App\Models\DeliveryMan', 'order_delivery_man')->withTrashed();
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
     * 微信支付二维码
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wechatPayUrl()
    {
        return $this->hasOne('App\Models\WechatPayUrl');
    }

    /**
     * 关联赠口
     *
     * @return $this
     */
    public function gifts()
    {
        return $this->belongsToMany(Goods::class, 'order_gift')->withTrashed()->withPivot('num', 'pieces');
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
        if ($status == $statusConf['non_confirm'] || $status == $statusConf['finished'] || $status == $statusConf['invalid']) {
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
     * 获取参加的促销活动
     *
     * @return mixed
     */
    public function getPromoAttribute()
    {
        if ($this->applyPromo) {
            return $this->applyPromo->promo->load(['condition', 'rebate']);
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
        if ($status == cons('order.status.invalid')) {
            //作废
            return 0;
        }
        if ($payType == cons('pay_type.online')) {
            //在线支付
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
        $reason = $this->orderReason->where('type', 0)->first();
        return $reason ? ['time' => (string)$reason->created_at, 'reason' => $reason->reason] : [];
    }

    /**
     * 获取作废原因
     *
     * @return string
     */
    public function getInvalidReasonAttribute()
    {
        $reason = $this->orderReason->where('type', 1)->first();
        return $reason ? ['time' => (string)$reason->created_at, 'reason' => $reason->reason] : [];
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
            $result = $status == cons('order.status.non_send') && ($this->pay_status <= cons('order.pay_status.payment_success'));
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
            && $this->attributes['status'] == cons('order.status.non_send');
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
        return $this->attributes['status'] >= cons('order.status.non_send');
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
        $payType = $this->pay_type;
        $statusArr = cons('order.status');
        $payStatusArr = cons('order.pay_status');
        $payTypeConf = cons('pay_type');
        $result = false;
        if ($payType == $payTypeConf['online']) {
            $result = $status >= $statusArr['non_send'];
        } elseif ($payType == $payTypeConf['cod']) {
            $result = $status > $statusArr['non_send'];
        }

        return $result && $this->attributes['is_cancel'] == cons('order.is_cancel.off') && $status < $statusArr['finished'] && $payStatus == $payStatusArr['non_payment'] && $this->attributes['pay_type'] != cons('pay_type.pick_up');
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
        return $this->attributes['pay_status'] == $orderConf['pay_status']['non_payment'] && $this->attributes['status'] != $orderConf['status']['invalid'] && is_null($this->wechatPayUrl);
    }

    /**
     * 是否可作废
     *
     * @return bool
     */
    public function getCanInvalidAttribute()
    {
        $orderConf = cons('order');
        $status = $this->status;
        $payStatus = $this->pay_status;
        return ($status > $orderConf['status']['non_confirm'] && $status < $orderConf['status']['finished']) && $payStatus == $orderConf['pay_status']['non_payment'];
    }

    /**
     * 获取订单优惠后价格
     *
     * @return mixed|string
     */
    public function getAfterRebatesPriceAttribute()
    {
        $price = $this->price;
        if (($display_fee = $this->display_fee) > 0) {
            return bcsub($price, $display_fee, 2);
        } elseif ($this->coupon_id && ($coupon = $this->coupon)) {
            $discount = $coupon->discount;
            $full = $coupon->full;
            if ($price < $full) {
                //订单价格小于优惠券满价格时优惠按比例
                $discount = bcmul($discount, bcdiv($price, $full, 2), 2);
            }
            return bcsub($price, $discount, 2);
        }

        return $price;
    }

    /**
     * 优惠了多少
     *
     * @return mixed
     */
    public function getHowMuchDiscountAttribute()
    {
        return $this->price - $this->getAfterRebatesPriceAttribute();
    }

    /**
     * 获取买家名
     *
     * @return string
     */
    public function getUserShopNameAttribute()
    {
        if ($this->user_id > 0) {
            return $this->user ? $this->user->shop_name : '';
        } elseif ($this->salesmanVisitOrder) {
            return $this->salesmanVisitOrder->customer_name;
        }
        return '';
    }

    /**
     * 获取买家联系人
     *
     * @return string
     */
    public function getUserContactAttribute()
    {
        if ($this->user_id > 0) {
            return $this->user ? $this->user->shop->contact_person : '';
        } elseif ($this->salesmanVisitOrder) {
            return $this->salesmanVisitOrder->salesmanCustomer->contact;
        }
    }

    /**
     * 获取买家名联系方式
     *
     * @return string
     */
    public function getUserContactInfoAttribute()
    {
        if ($this->user_id > 0) {
            return $this->user ? $this->user->shop->contact_info : '';
        } elseif ($this->salesmanVisitOrder) {
            return $this->salesmanVisitOrder->salesmanCustomer->contact_information;
        }
    }

    /**
     * 获取买家地址
     *
     * @return string
     */
    public function getUserShopAddressAttribute()
    {
        if ($this->user_id > 0) {
            return $this->user ? $this->user->shop->shopAddress : (new AddressData());
        } elseif ($this->salesmanVisitOrder) {
            return $this->salesmanVisitOrder->business_address;
        }
        return '';
    }

    /**
     * 获取买家类型
     *
     * @return string
     */
    public function getUserTypeNameAttribute()
    {
        return $this->user_id && $this->user ? $this->user->type_name : ($this->salesmanVisitOrder ? array_search($this->salesmanVisitOrder->customer_type,
            cons('user.type')) : 'retailer');
    }

    /**
     * 获取业务员
     *
     * @return string
     */
    public function getUserSalesmanAttribute()
    {


        /*  if ($this->user_id > 0) {
              return $this->user && $this->user->shop && $this->user->shop->salesmanCustomer ? $this->user->shop->salesmanCustomer->salesman_name : '';
          } else*/
        if ($this->salesmanVisitOrder) {
            return $this->salesmanVisitOrder->salesman_name;
        }
        return '';
    }

    /**
     * 获取卖家地址
     *
     * @return string
     */
    public function getShopAddressAttribute()
    {
        return $this->shop ? $this->shop->address : '';
    }

    /**
     * 获取卖家类型
     *
     * @return mixed|\WeiHeng\Constant\Constant
     */
    public function getShopUserTypeAttribute()
    {
        return $this->shop ? $this->shop->user_type : cons('user.type.wholesaler');
    }

    /**
     * 获取卖家名
     *
     * @return string
     */
    public function getShopNameAttribute()
    {
        return $this->shop ? $this->shop->name : '';
    }

    /**
     * 获取卖家联系方式
     *
     * @return string
     */
    public function getShopContactAttribute()
    {
        return $this->shop ? $this->shop->contact_person . '-' . $this->shop->contact_info : '';
    }

    /**
     * 拆分备注
     *
     * @return array
     */
    public function getRemarkGroupAttribute()
    {
        $remark = $this->remark;
        $group = [];
        $explode = '陈列费备注:';
        if (strstr($remark, $explode)) {
            $remarkGroup = explode($explode, $remark);
            $group['remark'] = str_replace('订单备注:', '', $remarkGroup[0]);
            $group['display'] = $explode . $remarkGroup[1];
        } else {
            $group['remark'] = $remark;
            $group['display'] = '';
        }
        return $group;
    }

    /**
     * 订单类型
     *
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return cons()->valueLang('order.type', $this->type);
    }

    /**
     * 获取支付类型名
     *
     * @return string
     */
    public function getPayTypeNameAttribute()
    {
        return cons()->valueLang('pay_type', $this->pay_type);
    }

    /**
     * 获取订单手续费
     *
     * @return int
     */
    public function getTargetFeeAttribute()
    {
        return $this->systemTradeInfo ? $this->systemTradeInfo->target_fee : 0;
    }

    /**
     * 获取实际支付金额
     *
     * @return mixed
     */
    public function getActualAmountAttribute()
    {
        return $this->systemTradeInfo ? $this->systemTradeInfo->amount : $this->after_rebates_price;
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
        if ($shopName && !is_numeric($shopName)) {
            return $query->where(function ($query) use ($shopName) {
                $query->whereHas('user.shop', function ($query) use ($shopName) {
                    $query->where('name', 'like', '%' . $shopName . '%');
                })->orWhere(function ($query) use ($shopName) {
                    $query->whereHas('salesmanVisitOrder.salesmanCustomer',
                        function ($query) use ($shopName) {
                            $query->where('name', 'like', '%' . $shopName . '%');
                        });
                });
            });
        }

    }

    /**
     * 按卖家名检索
     *
     * @param $query
     * @param $shopName
     * @return mixed
     */
    public function scopeOfShopName($query, $shopName)
    {

        if ($shopName) {
            return $query->whereHas('shop', function ($query) use ($shopName) {

                $query->where('name', 'like', '%' . $shopName . '%');
            });
        }
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
            ->orderBy('id', 'desc');
    }

    /**
     * 销售订单条件
     *
     * @param $query
     * @param $shopId
     * @return mixed
     */
    public function scopeOfSell($query, $shopId)
    {
        return $query->where('shop_id', $shopId);
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
     * 有效订单
     *
     * @param $query
     * @return mixed
     */
    public function scopeUseful($query)
    {
        return $query->where('is_cancel', 0);
    }

    /**
     * 未作废
     *
     * @param $query
     * @return mixed
     */
    public function scopeNoInvalid($query)
    {
        return $query->where('status', '<>', cons('order.status.invalid'));
    }

    /**
     * 待付款
     *
     * @param $query
     * @return mixed
     */
    public function scopeNonPayment($query)
    {
        return $query->where('pay_status', cons('order.pay_status.non_payment'))
            ->where(function ($query) {
                $statusArr = cons('order.status');
                $query->where('status', '<', $statusArr['finished'])
                    ->where(function ($query) use ($statusArr) {
                        $query->where(function ($query) use ($statusArr) {
                            $query->where('pay_type', cons('pay_type.online'))->where('status', '>=',
                                $statusArr['non_send']);
                        })->orWhere(function ($query) use ($statusArr) {
                            $query->where('pay_type', cons('pay_type.cod'))->where('status', '>',
                                $statusArr['non_send']);
                        });
                    });
            });
    }

    /**
     * 待确认
     *
     * @param $query
     * @return mixed
     */
    public function scopeWaitConfirm($query)
    {
        return $query->where('status', cons('order.status.non_confirm'))->useful();
    }

    /**
     * 待收款,货到付款或自提
     *
     * @param $query
     * @return mixed
     */
    public function scopeGetPayment($query)
    {

        return $query->where(function ($query) {
            $query->where(function ($q) {
                $q->where('pay_type', cons('pay_type.cod'))->where('status', cons('order.status.send'));
            })->orWhere(function ($q) {
                $q->where('pay_type', cons('pay_type.pick_up'))->where('status', cons('order.status.non_send'));
            });
        });
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
            ])->orWhere(function ($query) {
                $query->where('pay_type', cons('pay_type.cod'))->where('pay_status', '<=',
                    cons('order.pay_status.payment_success'));
            });
        })->where('status', cons('order.status.non_send'))->useful();
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
            if (array_get($search, 'pay_type')) {
                $query->where('pay_type', $search['pay_type']);
            }

            if (!is_null(array_get($search, 'type'))) {
                $query->where('type', $search['type']);
            }
            if (($status = array_get($search, 'status')) !== null) {
                if (in_array($status, array_keys(cons('order.pay_status')))) {
                    //查询未付款
                    $query->where([
                        'pay_status' => cons('order.pay_status.' . $status),
                        'status' => cons('order.status.non_send')
                    ]);
                } elseif ($status == 'non_send') {//未发货
                    $query->where(function ($query) {
                        $query->where([
                            'pay_type' => cons('pay_type.online'),
                            'pay_status' => cons('order.pay_status.payment_success')
                        ])->orWhere(function ($query) {
                            $query->where('pay_type', cons('pay_type.cod'))->where('pay_status', '<=',
                                cons('order.pay_status.payment_success'));
                        });
                    })->where('status', cons('order.status.non_send'));
                } elseif ($status == 'wait_receive') {//待收款
                    $query->getPayment();
                } else {
                    $query->where('status', cons('order.status.' . $status));
                }
            }
            $timeField = array_get($search, 'status') == 'finished' ? 'finished_at' : 'created_at';
            if (array_get($search, 'start_at')) {
                $query->where($timeField, '>=', $search['start_at']);
            }
            if (array_get($search, 'end_at')) {
                $endAt = (new Carbon($search['end_at']))->endOfDay();
                $query->where($timeField, '<=', $endAt);
            }
        });
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
     * 线上支付 未退款
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfRefund($query)
    {
        return $query->where('pay_type', cons('pay_type.online'))->where('pay_status', '<',
            cons('order.pay_status.refund'));
    }

    //未退款
    public function scopeNonRefund($query)
    {
        return $query->where('pay_status', '<', cons('order.pay_status.refund'));
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

    /**
     * 配送历史条件查询
     *
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeOfDeliverySearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            if ($deliveryManId = (int)array_get($search, 'delivery_man_id')) {
                $query->ofDeliveryMan($deliveryManId);
            }
            if (array_get($search, 'start_at')) {
                $query->where('delivery_finished_at', '>', $search['start_at']);
            }
            if ($endAt = array_get($search, 'end_at')) {
                $query->where('delivery_finished_at', '<', (new Carbon($endAt))->endOfDay());
            }
            $query->has('deliveryMan');
        });
    }

    /**
     * 查询配送人员
     *
     * @param $query
     * @param $deliveryMan_id
     * @return mixed
     */
    public function scopeOfDeliveryMan($query, $deliveryMan_id)
    {
        return $query->whereHas('deliveryMan', function ($query) use ($deliveryMan_id) {
            $query->where('id', $deliveryMan_id);
        });
    }

    /**
     * 查询订单商品
     *
     * @param $query
     */
    public function scopeOfOrderGoods($query)
    {
        return $query->with([
            'orderGoods' => function ($query) {
                $query->where('type', cons('order.goods.type.order_goods'))->with('goods');
            }
        ]);
    }


    /**
     * 按创建时间
     *
     * @param $query
     * @param null $startTime
     * @param null $endTime
     * @return mixed
     */
    public function scopeOfCreatedAt($query, $startTime = null, $endTime = null)
    {
        return $query->where(function ($query) use ($startTime, $endTime) {
            if ($startTime) {
                $query->where('created_at', '>', $startTime);
            }
            if ($endTime) {
                $query->where('created_at', '<', $endTime);
            }
        });
    }

    /**
     * 按支付方式
     *
     * @param $query
     * @param null $payType
     * @return mixed
     */
    public function scopeOfPayType($query, $payType = null)
    {
        $payTypes = cons('pay_type');
        if ($payType && in_array($payType, $payTypes)) {
            return $query->where('pay_type', $payType);
        }
    }

    /**
     * 订单id查询
     *
     * @param $query
     * @param $orderID
     * @return mixed
     */
    public function scopeOfOrderIdName($query, $idName)
    {
        if (!is_null($idName)) {
            $isID = is_numeric($idName) ? true : false;
            if ($isID) {
                return $query->where('id', $idName);
            } else {
                return $query->whereHas('user.shop', function ($query) use ($idName) {
                    $query->where('name', 'like', '%' . $idName . '%');
                });
            };
        }
    }
}
