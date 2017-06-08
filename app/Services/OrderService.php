<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderGoods;
use Carbon\Carbon;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Riverslei\Pusher\Pusher;
use App\Models\PushDevice;
use DB;


class OrderService extends BaseService
{

    /**
     * 推送信息到指定用户最近使用的移动设备
     *
     * @param $targetUserId
     * @return bool
     */
    public function push($targetUserId, $msg)
    {
        //只推送最近使用的移动设备
        $device = PushDevice::where('user_id', $targetUserId)->orderBy('updated_at', 'desc')->first();
        if (!$device) {
            return false;
        }
        $msgArray = ['title' => '', 'body' => $msg];
        if ($device->type == cons('push_device.iphone')) {
            $res = $this->pushIos($device->token, $msgArray);
        } else {
            $res = $this->pushAndroid($device->token, $msgArray);
        }
        if ($res) {//推送成功,推送条数+1
            $device->increment('send_count');
        }

        return $res;

    }

    /**
     * 推送到android设备
     *
     * @param $channelId
     * @param array $msg
     * @return mixed
     */
    public function pushAndroid($channelId, array $msg)
    {
        // 消息内容.
        $message = [
            // 消息的标题.
            'title' => $msg['title'],
            // 消息内容
            'description' => $msg['body']
        ];
        // 设置消息类型为 通知类型.
        $opts = [
            'msg_type' => cons('push_type.notice')
        ];

        // 向目标设备发送一条消息
        return Pusher::pushMsgToSingleDevice($channelId, $message, $opts);

    }

    /**
     * 推送到Ios设备
     *
     * @param $channelId
     * @param array $msg
     * @return mixed
     */
    public function pushIos($channelId, array $msg)
    {
        return PushNotification::app('IOS')->to($channelId)->send($msg['title'] . $msg['body']);
    }

    /**
     * 订单提交逻辑处理
     *
     * @param $data
     * @return mixed
     */
    public function orderSubmitHandle($data)
    {

        $result = DB::transaction(function () use ($data) {
            $user = auth()->user();

            $carts = $user->carts()->where('status', 1)->with('goods')->get()->each(function ($cart) {
                $cart->goods->setAppends([]);
            });
            if ($carts->isEmpty()) {
                $this->setError('购物车为空');
                return false;
            }
            $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
            foreach ($carts as $cart) {
                $orderGoodsNum[$cart->goods_id] = $cart->num;
            }
            //验证
            $cartService = new CartService($carts);

            // 是否送货订单
            $isDelivery = isset($data['pay_type']);


            if (!$shops = $cartService->validateOrder($orderGoodsNum, false, $isDelivery)) {
                return false;
            }

            if ($isDelivery) {
                $result = $this->_submitOrderByDelivery($data, $shops);
            } else {
                $result = $this->_submitOrderByPickUp($data, $shops);
            }
            if ($result) {
                // 删除购物车
                $goodsNum = $user->carts()->where('status', 1)->delete();
                //减少购物车数量
                (new CartService)->decrement($goodsNum);

                //提交订单成功发送短信
                //$this->sendSmsToSeller($result['success_orders']);
            }
            return $result;

        });
        return is_array($result) ? array_except($result, 'success_orders') : $result;
    }


    /**
     * 发送短信
     *
     * @param $orders
     * @return bool
     */
    public function sendSmsToSeller($orders)
    {
        if (count($orders) == 0) {
            return false;
        }
        foreach ($orders as $order) {
            $data = [
                'order_id' => $order->id,
                'order_amount' => $order->price ,
                'pay_type' => cons()->valueLang('pay_type', $order->pay_type)
            ];
            app('pushbox.sms')->queue('order', $order->shop->user_backup_mobile, $data);
        }
        return true;
    }

    /**
     * 根据订单返回优惠券
     *
     * @param $order
     * @return bool
     */
    public function backCoupon($order)
    {
        if (!$order->coupon_id || is_null($order->coupon)) {
            return true;
        }

        $user = $order->user;

        $coupon = $user->coupons()->find($order->coupon_id);

        if (is_null($coupon)) {
            return false;
        }

        if ($coupon->pivot->fill(['used_at' => null])->save()) {
            return true;
        }
        return false;
    }

    /**
     * 订单修改处理
     *
     * @param $order
     * @param $attributes
     * @param $userId
     * @return array
     */
    public function changeOrder($order, $attributes, $userId)
    {
        //判断待修改物品是否属于该订单
        $orderGoods = OrderGoods::find(intval($attributes['pivot_id']));
        if (!$orderGoods || $orderGoods->order_id != $order->id) {
            $this->setError( '订单不存在');
            return false;
        }
        $num = $attributes['num'];

        $price = isset($attributes['price']) ? $attributes['price'] : $orderGoods->price;

        if ($num == $orderGoods->num && $price == $orderGoods->price) {
            return true;
        }
        $flag = DB::transaction(function () use ($orderGoods, $order, $price, $num, $userId) {
            $oldTotalPrice = $orderGoods->total_price;
            $newTotalPrice = bcmul($num, $price, 2);
            //增加修改记录
            $oldNum = $orderGoods->num;
            $oldPrice = $orderGoods->price;

            $newOrderPrice = bcadd(bcsub($order->price, $oldTotalPrice, 2), $newTotalPrice, 2);

            if ($order->display_fee >= $newOrderPrice) {
                //订单陈列费不能大于订单价格
                $this->setError( '订单陈列费不能大于订单价格');
                return false;
            }
            $orderGoods->fill(['price' => $price, 'num' => $num, 'total_price' => $newTotalPrice])->save();
            // 价格不同才更新
            $oldTotalPrice != $newTotalPrice && $order->fill(['price' => $newOrderPrice])->save();

            //如果有业务订单修改业务订单
            if (!is_null($salesmanVisitOrder = $order->salesmanVisitOrder)) {
                $salesmanVisitOrder->fill(['amount' => $order->price])->save();

                $fill = [];
                if ($oldPrice == 0) {
                    //商品原价为0时更新抵费商品
                    $salesmanVisitOrderGoods = $salesmanVisitOrder->mortgageGoods()->where('goods_id',
                        $orderGoods->goods_id)->first();
                    if ($salesmanVisitOrderGoods) {
                        $salesmanVisitOrderGoods = $salesmanVisitOrderGoods->pivot;
                        $fill = ['used' => $num];
                    }

                } else {
                    $salesmanVisitOrderGoods = $salesmanVisitOrder->orderGoods()->where('goods_id',
                        $orderGoods->goods_id)->first();

                    if ($salesmanVisitOrderGoods) {
                        $fill = [
                            'price' => $price,
                            'num' => $num,
                            'amount' => $newTotalPrice
                        ];
                    }

                }
                !is_null($salesmanVisitOrderGoods) && $salesmanVisitOrderGoods->fill($fill)->save();
            }


            $content = "商品编号：{$orderGoods->goods_id}; 原商品数量：{$oldNum},现商品数量：{$orderGoods->num}; 原商品价格：{$oldPrice},现商品价格：{$orderGoods->price}";

            //添加记录
            $this->addOrderChangeRecord($order, $content, $userId);

            //通知买家订单价格发生了变化
            $redisKey = 'push:user:' . $order->user_id;
            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.price_changed');
            (new RedisService)->setRedis($redisKey, $redisVal,  cons('push_time.msg_life'));

            return true;
        });
        return $flag;
    }

    /**
     * 添加订单修改记录
     *
     * @param $order
     * @param $content
     * @param null $userId
     */
    public function addOrderChangeRecord($order, $content, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        $order->orderChangeRecode()->create([
            'user_id' => $userId,
            'content' => $content
        ]);
    }

    /**
     * 验证优惠券
     *
     * @param $shopId
     * @param $couponId
     * @param $sumPrice
     * @return bool
     */
    public function validateCouponId($shopId, $couponId, $sumPrice)
    {
        $coupon = auth()->user()->coupons()->wherePivot('used_at', null)->OfUseful($shopId, $sumPrice)->find($couponId);

        if ($coupon) {
            $pivot = $coupon->pivot;
            if ($pivot->fill(['used_at' => Carbon::now()])->save()) {

                return $couponId;
            }
            return false;
        }
        return false;
    }

    /**
     * 拆分订单商品
     *
     * @param $order
     * @return array
     */
    public function explodeOrderGoods($order)
    {
        //订单商品
        $orderGoods = collect();
        //陈列费商品
        $mortgageGoods = collect();

        foreach ($order->goods as $item) {
            if ($item->pivot->type == cons('order.goods.type.mortgage_goods')) {
                $mortgageGoods->push($item);
                continue;
            }
            $orderGoods->push($item);
        }
        $order->addHidden(['goods']);
        return compact('orderGoods', 'mortgageGoods');
    }

    /**
     * 获取票据单号
     *
     * @param $shopId
     * @return string
     */
    public function getNumbers($shopId)
    {
        $carbon = (new Carbon());
        $month = $carbon->copy()->format('Y-m');
        $day = $carbon->copy()->toDateString();

        $like = $month . '-%';

        $orderCount = Order::where('numbers', 'like', $like)->where('shop_id', $shopId)->count();

        $number = $day . '-' . str_pad($orderCount + 1, 4, '0', STR_PAD_LEFT);
        return $number;
    }


    /**
     * 订单提交失败时删除已成功的订单
     *
     * @param $successOrders
     * @return bool
     */
    private function _deleteSuccessOrders($successOrders)
    {
        foreach ($successOrders as $successOrder) {
            $successOrder->delete();
        }
        return true;
    }

    /**
     * 添加上门自提订单
     *
     * @param $data
     * @param $shops
     * @return array|bool
     */
    private function _submitOrderByPickUp($data, $shops)
    {
        $pid = $this->_getOrderPid($shops);

        $successOrders = collect([]);  //保存提交成功的订单

        $payType = cons('pay_type.pick_up');

        foreach ($shops as $shop) {
            $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';
            $orderData = [
                'pid' => $pid,
                'user_id' => auth()->id(),
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'remark' => $remark,
                'pay_type' => $payType
            ];
            $couponId = isset($data['shop'][$shop->id]['coupon_id']) ? $data['shop'][$shop->id]['coupon_id'] : null;
            if ($couponId) {
                $couponId = $this->validateCouponId($shop->id, $couponId, $shop->sum_price);
                $couponId && ($orderData['coupon_id'] = $couponId);
            }
            $order = Order::create($orderData);

            if ($order->exists) {//添加订单成功,修改orderGoods中间表信息
                $successOrders->push($order);
                $orderGoods = [];
                foreach ($shop->cart_goods as $cartGoods) {
                    $pickUpPrice = $cartGoods->goods->pick_up_price;
                    $cartGoodsNum = $cartGoods->num;
                    $orderGoods[] = new OrderGoods([
                        'goods_id' => $cartGoods->goods_id,
                        'price' => $pickUpPrice,
                        'num' => $cartGoodsNum,
                        'pieces' => $cartGoods->goods->pieces_id,
                        'total_price' => bcmul($pickUpPrice, $cartGoodsNum, 2)
                    ]);
                }
                if ($order->orderGoods()->saveMany($orderGoods)) {
                    $redisKey = 'push:seller:' . $shop->user_id;
                    $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.non_send.pick_up');
                    (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
                } else {
                    $this->_deleteSuccessOrders($successOrders);
                    $this->setError('订单提交时出现问题');
                    return false;
                }

            } else {
                $this->_deleteSuccessOrders($successOrders);
                $this->setError('订单提交时出现问题');
                return false;
            }
        }
        return [
            'pay_type' => $payType,
            'sum_price' => $successOrders->sum('price'),
            'type' => "",
            'success_orders' => $successOrders,
        ];
    }

    /**
     * 添加送货订单
     *
     * @param $data
     * @param $shops
     * @return array|bool
     */
    private function _submitOrderByDelivery($data, $shops)
    {
        $payTypes = cons('pay_type');
        $payType = array_get($payTypes, $data['pay_type'], head($payTypes));

        $payWay = 0;
        if (isset($data['pay_way'])) {
            $payWayConf = cons('pay_way.cod');
            $payWay = array_get($payWayConf, $data['pay_way'], head($payWayConf));
        }
        $shippingAddressService = new ShippingAddressService();

        //验证收货地址是否合法
        if (!isset($data['shipping_address_id']) || !$shippingAddressService->validate($data['shipping_address_id'],
                null, $shops)
        ) {
            $this->setError('收货地址不存在');
            return false;
        }
        $shippingAddressId = $data['shipping_address_id'];

        $pid = $this->_getOrderPid($shops);

        $successOrders = collect([]);  //保存提交成功的订单

        foreach ($shops as $shop) {
            $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';

            $orderData = [
                'pid' => $pid,
                'user_id' => auth()->id(),
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                'pay_way' => $payWay,
                'shipping_address_id' => $shippingAddressService->copyToSnapshot($shippingAddressId),
                'remark' => $remark
            ];

            $couponId = isset($data['shop'][$shop->id]['coupon_id']) ? $data['shop'][$shop->id]['coupon_id'] : null;
            if ($couponId) {
                $couponId = $this->validateCouponId($shop->id, $couponId, $shop->sum_price);
                $couponId && ($orderData['coupon_id'] = $couponId);
            }
            if (!$orderData['shipping_address_id']) {
                $this->_deleteSuccessOrders($successOrders);
                return false;
            }

            $order = Order::create($orderData);
            if ($order->exists) {//添加订单成功,修改orderGoods中间表信息
                $successOrders->push($order);
                $orderGoods = [];
                foreach ($shop->cart_goods as $cartGoods) {
                    $orderGoods[] = new OrderGoods([
                        'goods_id' => $cartGoods->goods_id,
                        'price' => $cartGoods->goods->price,
                        'num' => $cartGoods->num,
                        'pieces' => $cartGoods->goods->pieces_id,
                        'total_price' => $cartGoods->goods->price * $cartGoods->num,
                    ]);
                }
                if ($order->orderGoods()->saveMany($orderGoods)) {
                    if ($payType == $payTypes['cod']) {
                        //货到付款订单直接通知卖家发货
                        $redisKey = 'push:seller:' . $shop->user_id;
                        $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.non_send.cod');
                        (new RedisService)->setRedis($redisKey, $redisVal, cons('push_time.msg_life'));
                    }
                } else {
                    $this->_deleteSuccessOrders($successOrders);
                    $this->setError('订单提交时出现问题');
                    return false;
                }

            } else {
                $this->_deleteSuccessOrders($successOrders);
                $this->setError('订单提交时出现问题');
                return false;
            }
        }
        $returnArray = [
            'pay_type' => $payType,
            'success_orders' => $successOrders,
            'sum_price' => $successOrders->sum('price'),
            'type' => ""
        ];

        if ($payType == $payTypes['online']) {
            if ($pid > 0) {
                $returnArray['order_id'] = $pid;
                $returnArray['type'] = 'all';
            } else {
                $returnArray['order_id'] = $successOrders->first()->id;
            }
        }
        return $returnArray;
    }


    /**
     * 获取订单pid
     *
     * @param $shops
     * @return int
     */
    private function _getOrderPid($shops)
    {
        return $shops->count() > 1 ? Order::max('pid') + 1 : 0;
    }

}
