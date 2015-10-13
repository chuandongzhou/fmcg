<?php

namespace App\Http\Controllers\Index;

use App\Jobs\PushOrderMsg;
use App\Models\OrderGoods;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public $userId;//用户ID
    public $userType;//用户类型


    public function __construct()
    {
        $this->userId = auth()->user()->id;
        $this->userType = auth()->user()->type;
        session(['id' => $this->userId]);
        session(['type' => $this->userType]);
        session('shop_id') ?: session(['shop_id' => auth()->user()->shop->id]);

    }

    /**
     * 批量取消订单确认状态，在线支付：确认但未付款，可取消；货到付款：确认但未发货，可取消
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function putCancelSure(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        $status = Order::where(function ($query) {
            $query->wherehas('shop.user', function ($query) {
                $query->where('id', $this->userId);
            })->orWhere('user_id', $this->userId);
        })->whereIn('id', $orderIds)->where('pay_status',
            cons('order.pay_status.non_payment'))->nonCancel()->nonSend()->update([
            'is_cancel' => cons('order.is_cancel.on'),
            'cancel_by' => $this->userId,
            'cancel_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $status ? $this->success() : $this->error('操作失败');
    }


    /**
     * 确认订单消息
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\View\View
     */
    public function postConfirmOrder(Request $request)
    {
        $attributes = $request->all();

        $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
        foreach ($attributes['goods_id'] as $goodsId) {
            if ($attributes['num'][$goodsId] > 0) {
                $orderGoodsNum[$goodsId] = $attributes['num'][$goodsId];
            }
        }

        if (empty($orderGoodsNum)) {
            return redirect()->back()->withInput();
        }
        $confirmedGoods = auth()->user()->carts()->whereIn('goods_id', array_keys($orderGoodsNum));

        $carts = $confirmedGoods->with('goods')->get();

        //验证
        $cartService = new CartService($carts);

        if (!$cartService->validateOrder($orderGoodsNum, true)) {
            return redirect()->back()->withInput();
        }

        if ($confirmedGoods->update(['status' => 1])) {
            return redirect('order/confirm-order');
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * 确认订单页
     *
     * @return \Illuminate\View\View
     */
    public function getConfirmOrder()
    {
        $carts = auth()->user()->carts()->where('status', 1)->with('goods')->get();
        $shops = (new CartService($carts))->formatCarts();
        //收货地址
        $shippingAddress = auth()->user()->shippingAddress()->with('address')->get();

        return view('index.order.confirm-order', ['shops' => $shops, 'shippingAddress' => $shippingAddress]);
    }


    /**
     * 提交订单
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubmitOrder(Request $request)
    {
        $carts = auth()->user()->carts()->where('status', 1)->with('goods')->get();

        if (empty($carts[0])) {
            return redirect()->back()->withInput();
        }
        $orderGoodsNum = [];  //存放商品的购买数量  商品id => 商品数量
        foreach ($carts as $cart) {
            $orderGoodsNum[$cart->goods_id] = $cart->num;
        }
        //验证
        $cartService = new CartService($carts);

        if (!$shops = $cartService->validateOrder($orderGoodsNum)) {
            return redirect('cart');
        }

        $data = $request->all();

        $payTypes = cons('pay_type');
        $codPayTypes = cons('cod_pay_type');

        $onlinePaymentOrder = [];   //  保存在线支付的订单
        $payType = array_get($payTypes, $data['pay_type'], head($payTypes));

        $codPayType = $payType == $payTypes['cod'] ? array_get($codPayTypes, $data['cod_pay_type'],
            head($codPayTypes)) : 0;
        //TODO: 需要验证收货地址是否合法
        $shippingAddressId = $data['shipping_address_id'];
        //$remark = $data['remark'] ? $data['remark'] : '';
        foreach ($shops as $shop) {
            $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';
            $orderData = [
                'user_id' => auth()->user()->id,
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                'status' => cons('order.status.non_send'),
                'cod_pay_type' => $codPayType,
                'shipping_address_id' => $shippingAddressId,
                'remark' => $remark
            ];
            $order = Order::create($orderData);
            if ($order->exists) {
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
                    }
                    // 删除购物车
                    auth()->user()->carts()->where('status', 1)->delete();
                    return redirect('order-buy');
                } else {
                    //TODO: 跳转页面后期修改
                    $order->delete();

                    return redirect('cart');
                }

            } else {
                //跳转页面后期修改
                return redirect('cart');
            }
        }
        // TODO: 跳至支付页面->写入到redis,同时设置过期时间.
        //set push:seller:$seller_id
        //expire push:seller:$seller_id 600  (10分钟)
        dd($onlinePaymentOrder);

    }

    /**
     * 订单统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getStatistics(Request $request)
    {
        //订单对象类型
        $objType = cons()->valueLang('user.type');
        array_forget($objType, $this->userType);
        //支付类型
        $payType = cons()->valueLang('pay_type');
        //查询条件判断
        $search = $request->all();
        $orderCurrent = isset($search['order_page_num']) ? intval($search['order_page_num']) : 1;
        $goodsCurrent = isset($search['goods_page_num']) ? intval($search['goods_page_num']) : 1;
        $per = 2;//分页数
        $statistics = $this->_searchAllOrderByOptions($search);
        $orderCount = $statistics->count();//订单总数
        $stat = $statistics->forPage($orderCurrent, $per);
        $res = $this->_orderStatistics($statistics->toArray());
        $goodsCount = count($res['goods']);//商品总数
        $otherStat = $res['stat'];
        $statGoods = collect($res['goods'])->forPage($goodsCurrent, $per);

        $search['checkbox_flag'] = isset($search['checkbox_flag']) ? $search['checkbox_flag'] : 1;

        $orderNav = $this->_pageNav($orderCurrent, $per, $orderCount);
        $goodsNav = $this->_pageNav($goodsCurrent, $per, $goodsCount, 1);

        return view('index.order.order-statistics', [
            'search' => $search,
            'pay_type' => $payType,
            'obj_type' => $objType,
            'statistics' => $stat->toArray(),
            'otherStat' => $otherStat,
            'goods' => $statGoods->toArray(),
            'orderNav' => $orderNav,
            'goodsNav' => $goodsNav,
            'orderCurrent' => $orderCurrent,
            'goodsCurrent' => $goodsCurrent
        ]);
    }

    private function _pageNav($pageNum, $per, $pageCount, $flag = 0)
    {
        $html = '';
        if ($pageNum > 1) {
            $html .= '<button class="prev' . $flag . '">上一页</button>';
        }

        if (($pageCount / $per) > $pageNum) {
            $html .= '<button class="next' . $flag . '">下一页</button>';
        }

        return $html;
    }

    /**
     * 根据查询条件查询满足条件的订单
     *
     * @param $search
     * @return mixed
     */
    private function _searchAllOrderByOptions($search)
    {
        return Order::where(function ($query) use ($search) {
            //付款方式
            if (empty($search['pay_type'])) {
                $query->where(function ($query) {
                    //在线支付情况，只查询付款完成以后的状态
                    $query->ofPaySuccess();
                })->orWhere(function ($query) {
                    //货到付款情况，只查询发货以后的状态
                    $query->ofHasSend();
                });
            } else {
                if ($search['pay_type'] == cons('pay_type.online')) {//在线支付
                    $query->ofPaySuccess();
                } else { //货到付款
                    $query->ofHasSend();
                }
            }
            //查询买家
            if (!empty($search['obj_type']) && $search['obj_type'] < $this->userId) {
                $query->where('user_id', intval($search['obj_type']));
            }
            //時間
            if (!empty($search['start_at']) && !empty($search['end_at'])) {
                $query->whereBetween('created_at', [$search['start_at'], $search['end_at']]);
            }

        })->wherehas('goods', function ($query) use ($search) {

            empty($search['goods_name']) ?: $query->where('name', trim($search['goods_name']));

        })->wherehas('shop.user', function ($query) use ($search) {//根据卖家姓名,类型查询

            empty($search['seller_name']) ?: $query->where('user_name', trim($search['seller_name']));

            //查询卖家
            if (!empty($search['obj_type']) && $search['obj_type'] > $this->userId) {
                $query->where('id', intval($search['obj_type']));
            }
        })->wherehas('shop.shopAddress', function ($query) use ($search) {//根据店铺地址查询

            empty($search['province_id']) ?: $query->where('province_id', $search['province_id']);


            empty($search['city_id']) ?: $query->where('city_id', $search['city_id']);


            empty($search['district_id']) ?: $query->where('district_id', $search['district_id']);

        })->nonCancel()->with('user', 'shippingAddress', 'goods')->orderBy('id', 'desc')->get();
    }

    /**
     * 订单和商品统计
     *
     * @param array $res
     * @return mixed
     */
    private function _orderStatistics(array $res)
    {
        $stat['totalNum'] = count($res);//订单总数
        $stat['totalAmount'] = 0;//订单总金额
        //在线支付订单
        $stat['onlineNum'] = 0;
        $stat['onlineAmount'] = 0;
        //货到付款订单
        $stat['codNum'] = 0;
        $stat['codAmount'] = 0;
        $stat['codReceiveAmount'] = 0;//实收金额
        $goodStat = [];
        foreach ($res as $value) {
            //订单相关统计
            $stat['totalAmount'] += $value['price'];
            if ($value['pay_type'] == cons('pay_type.online')) {
                ++ $stat['onlineNum'];
                $stat['onlineAmount'] += $value['price'];
            } else {
                ++ $stat['codNum'];
                $stat['codAmount'] += $value['price'];
                if ($value['pay_status'] == cons('order.pay_status.payment_success')) {
                    $stat['codReceiveAmount'] += $value['price'];
                }
            }
            //商品相关统计
            foreach ($value['goods'] as $good) {
                $num = $good['pivot']['num'];
                $price = $good['pivot']['price'] * $num;
                $name = $good['name'];
                $id = $good['id'];
                if (isset($goodStat[$good['id']])) {
                    $goodStat[$good['id']]['num'] += $num;
                    $goodStat[$good['id']]['price'] += $price;
                } else {

                    $goodStat[$good['id']] = ['id' => $id, 'name' => $name, 'price' => $price, 'num' => $num];
                }
            }
        }

        return ['stat' => $stat, 'goods' => $goodStat];
    }

    /**
     * 网页端信息提示
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderPolling()
    {
        $redis = Redis::connection();

        //遍历redis键,过期时间小于5分钟的放入推送列表
        $targetUserIds = $redis->keys('push:user:*');
        $targetSellerIds = $redis->keys('push:seller:*');
        $ids = array_merge($targetUserIds, $targetSellerIds);
        if (empty($ids)) {
            return response()->json([]);
        }
        foreach ($ids as $id) {
            if ($redis->ttl($id) < 300) {
                $this->dispatch(new PushOrderMsg(substr($id, strrpos($id, ':') + 1)));
                $redis->del($id);
            }
        }
        //push:user:$user_id买家是否有提醒
        if ($redis->exists('push:user:' . $this->userId)) {
            $redis->del('push:user:' . $this->userId);

            return response()->json(['type' => 'user', 'data' => $this->userId]);
        }
        //push:seller:$seller_id卖家是否有提醒
        $shopId = session('shop_id');

        if ($redis->exists('push:seller:' . $this->userId)) {

            $redis->del('push:seller:' . $this->userId);

            return response()->json(['type' => 'shop', 'data' => $shopId]);
        }

        return response()->json([]);
    }

    public function postExport(Request $request)
    {
        //查询条件判断
        $search = $request->all();

        $stat = $this->_searchAllOrderByOptions($search);

        $otherStat = $this->_orderStatistics($stat['data']);
        dd($stat, $otherStat);
    }
}
