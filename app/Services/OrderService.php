<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderGoods;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use App\Http\Requests;
use Riverslei\Pusher\Pusher;
use App\Models\PushDevice;
use DB;


class OrderService
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
            $device->send_count += $device->send_count;
            $device->save();
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

            $carts = $user->carts()->where('status', 1)->with('goods')->get();
            if ($carts->isEmpty()) {
                return false;
            }
            $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
            foreach ($carts as $cart) {
                $orderGoodsNum[$cart->goods_id] = $cart->num;
            }
            //验证
            $cartService = new CartService($carts);

            if (!$shops = $cartService->validateOrder($orderGoodsNum)) {
                return false;
            }

            $payTypes = cons('pay_type');
            $codPayTypes = cons('cod_pay_type');

            $onlinePaymentOrder = [];   //  保存在线支付的订单
            $payType = array_get($payTypes, $data['pay_type'], head($payTypes));

            $codPayType = $payType == $payTypes['cod'] ? array_get($codPayTypes, $data['cod_pay_type'],
                head($codPayTypes)) : 0;
            //TODO: 需要验证收货地址是否合法
            $shippingAddressId = $data['shipping_address_id'];

            $pid = $shops->count() > 1 ? Order::max('pid') + 1 : 0;

            $successOrders = [];  //保存提交成功的订单

            foreach ($shops as $shop) {
                $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';
                $orderData = [
                    'pid' => $pid,
                    'user_id' => auth()->user()->id,
                    'shop_id' => $shop->id,
                    'price' => $shop->sum_price,
                    'pay_type' => $payType,
                    'cod_pay_type' => $codPayType,
                    'shipping_address_id' => (new ShippingAddressService)->copyToSnapshot($shippingAddressId),
                    'remark' => $remark
                ];
                if (!$orderData['shipping_address_id']) {
                    foreach ($successOrders as $successOrder) {
                        $successOrder->delete();
                    }
                    return false;
                }
                $order = Order::create($orderData);
                if ($order->exists) {//添加订单成功,修改orderGoods中间表信息
                    $successOrders[] = $order;
                    $orderGoods = [];
                    foreach ($shop->cart_goods as $cartGoods) {
                        $orderGoods[] = new OrderGoods([
                            'goods_id' => $cartGoods->goods_id,
                            'price' => $cartGoods->goods->price,
                            'num' => $cartGoods->num,
                            'total_price' => $cartGoods->goods->price * $cartGoods->num,
                        ]);
                    }
                    if ($order->orderGoods()->saveMany($orderGoods)) {
                        if ($payType == $payTypes['online']) {
                            $onlinePaymentOrder[] = $order->id;
                        } else {
                            //货到付款订单直接通知卖家发货
                            $redisKey = 'push:seller:' . $shop->user->id;
                            $redisVal = '您的订单' . $order->id . ',' . cons()->lang('push_msg.non_send.cod');
                            (new RedisService)->setRedis($redisKey, $redisVal);
                        }
                    } else {
                        foreach ($successOrders as $successOrder) {
                            $successOrder->delete();
                        }
                        return false;
                    }

                } else {
                    return false;
                }
            }
            // 删除购物车
            $user->carts()->where('status', 1)->delete();
            // 增加商品销量
            GoodsService::addGoodsSalesVolume($orderGoodsNum);

            $returnArray = [
                'pay_type' => $payType,
                'type' => ""
            ];

            if ($payType == $payTypes['online']) {
                if ($pid > 0) {
                    $returnArray['order_id'] = $pid;
                    $returnArray['type'] = 'all';
                } else {
                    $returnArray['order_id'] = $onlinePaymentOrder[0];
                }
            }
            return $returnArray;
        });
        return $result;
    }
}
