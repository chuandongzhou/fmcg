<?php

namespace App\Http\Controllers\Index;

use App\Models\OrderGoods;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\RedisService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Facades\Excel;
use Gate;

class OrderController extends Controller
{
    public $user;


    public function __construct()
    {
        $this->user = auth()->user();
    }


    /**
     * 确认订单消息
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\View\View
     */
    public function postConfirmOrder(Request $request)
    {
        $orderGoodsNum = $request->input('num');
        $goodsId = $request->input('goods_id');

        $orderGoodsNum = array_where($orderGoodsNum, function ($key, $value) use ($goodsId) {
            return in_array($key, $goodsId);
        });

        if (empty($orderGoodsNum)) {
            return redirect()->back()->withInput();
        }
        $confirmedGoods = $this->user->carts()->whereIn('goods_id', array_keys($orderGoodsNum));

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
        $carts = $this->user->carts()->where('status', 1)->with('goods')->get();
        $shops = (new CartService($carts))->formatCarts();
        //收货地址
        $shippingAddress = $this->user->shippingAddress()->with('address')->get();

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
        $carts = $this->user->carts()->where('status', 1)->with('goods')->get();
        if (empty($carts[0])) {
            return redirect('cart');
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
        $pid = 0;
        if ($shops->count() > 1) {
            $maxPid = Order::max('pid');
            $pid = $maxPid + 1;
        }

        $successOrders = [];  //保存提交成功的订单

        foreach ($shops as $shop) {
            $remark = $data['shop'][$shop->id]['remark'] ? $data['shop'][$shop->id]['remark'] : '';
            $orderData = [
                'pid' => $pid,
                'user_id' => $this->user->id,
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                'cod_pay_type' => $codPayType,
                'shipping_address_id' => $shippingAddressId,
                'remark' => $remark
            ];
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
                        RedisService::setRedis($redisKey, $redisVal);
                    }
                } else {
                    foreach ($successOrders as $successOrder) {
                        $successOrder->delete();
                    }

                    return redirect('cart');
                }

            } else {
                //跳转页面后期修改
                return redirect('cart');
            }
        }


        // 删除购物车
        $this->user->carts()->where('status', 1)->delete();
        // 增加商品销量
        GoodsService::addGoodsSalesVolume($orderGoodsNum);

        $query = $pid > 0 ? '?type-all&order_id=' . $pid : (empty($onlinePaymentOrder) ? 0 : '?order_id='
            . $onlinePaymentOrder[0]);

        $redirectUrl = empty($onlinePaymentOrder) ? url('order-buy') : url('order/finish-order' . $query);

        return redirect($redirectUrl);
    }

    public function getFinishOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        if (is_null($orderId)) {
            return redirect(url('order-buy'));
        }

        $type = $request->input('type');
        $field = $type == 'all' ? 'pid' : 'id';

        $orders = Order::where('id', $field)->get(['pay_type', 'pay_status', 'user_id']);
        if (Gate::denies('validate-online-orders', $orders)) {
            return redirect(url('order-buy'));
        }

        return view('index.order.finish-order', ['orderId' => $orderId, 'type' => $type]);
    }

    /**
     * 订单统计,全部显示商家名字
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getStatistics(Request $request)
    {
        //订单对象类型
        $objType = cons()->valueLang('user.type');
        array_forget($objType, $this->user->type);
        //支付类型
        $payType = cons()->valueLang('pay_type');
        //查询条件判断
        $search = $request->all();
        $orderCurrent = isset($search['order_page_num']) ? intval($search['order_page_num']) : 1;
        $goodsCurrent = isset($search['goods_page_num']) ? intval($search['goods_page_num']) : 1;
        $per = cons('statistics_per');//分页数
        $statistics = $this->_searchAllOrderByOptions($search);
        $orderCount = $statistics->count();//订单总数
        $stat = $statistics->forPage($orderCurrent, $per);
        $res = $this->_orderStatistics($statistics);
        $goodsCount = count($res['goods']);//商品总数
        $otherStat = $res['stat'];
        $statGoods = collect($res['goods'])->forPage($goodsCurrent, $per);

        $search['checkbox_flag'] = isset($search['checkbox_flag']) ? $search['checkbox_flag'] : 1;

        $orderNav = $this->_pageNav($orderCurrent, $per, $orderCount);
        $goodsNav = $this->_pageNav($goodsCurrent, $per, $goodsCount, 1);
        $objOfShow = isset($search['obj_type']) ? $search['obj_type'] : 0;
        $showObjName = $this->_inputName($objOfShow);

        return view('index.order.order-statistics', [
            'search' => $search,
            'pay_type' => $payType,
            'obj_type' => $objType,
            'objCurrentType' => $request->input('obj_type') ? $request->input('obj_type') : '',
            'statistics' => $stat,
            'otherStat' => $otherStat,
            'goods' => $statGoods,
            'orderNav' => $orderNav,
            'goodsNav' => $goodsNav,
            'orderCurrent' => $orderCurrent,
            'goodsCurrent' => $goodsCurrent,
            'showObjName' => $showObjName
        ]);
    }

    /**
     * user_name显示信息
     *
     * @param $objType
     * @return string
     */
    private function _inputName($objType)
    {
        if ($this->user->type == cons('user.type.retailer')
            || ($this->user->type == cons('user.type.wholesaler')
                && $objType == cons('user.type.supplier'))
        ) {
            return '卖家名称';
        }

        return '买家名称';
    }

    /**
     * 分页
     *
     * @param $pageNum
     * @param $per
     * @param $count
     * @param int $flag
     * @return string
     */
    private function _pageNav($pageNum, $per, $count, $flag = 0)
    {
        $pageTotal = $count / $per;
        $html = '';
        if ($pageTotal > 1) {
            $html .= '<ul class="pager">';
            if ($pageNum > 1) {
                $html .= '<li><a class="prev' . $flag . '">上一页</a></li>';
            }

            if ($pageTotal > $pageNum) {
                $html .= '<li><a class="next' . $flag . '">下一页</a></li>';
            }
            $html .= '</ul>';
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
        $query = Order::where(function ($query) use ($search) {
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
        });

        if (!empty($search['obj_type']) && $search['obj_type'] < $this->user->type
            || $this->user->type == cons('user.type.supplier')
        ) {//查询买家
            $query->wherehas('shop.user', function ($q) use ($search) {
                $q->where('id', $this->user->id);
            });
            if (!empty($search['obj_type'])) {
                $query->wherehas('user', function ($q) use ($search) {
                    $q->where('type', $search['obj_type']);

                });
            }
        }
        if (!empty($search['obj_type']) && $search['obj_type'] > $this->user->type
            || $this->user->type == cons('user.type.retailer')
        ) {//查询卖家
            $query->where('user_id', $this->user->id);
            if (!empty($search['obj_type'])) {
                $query->wherehas('shop.user', function ($q) use ($search) {
                    $q->where('type', $search['obj_type']);
                });
            }
        }
        //时间
        if (!empty($search['start_at']) && !empty($search['end_at'])) {
            $query->whereBetween('created_at', [$search['start_at'], $search['end_at']]);
        }
        //商品名
        if (!empty($search['goods_name'])) {
            $query->wherehas('goods', function ($q) use ($search) {
                $q->where('name', trim($search['goods_name']));

            });
        }
        //用户名
        if (!empty($search['user_name'])) {
            if ($this->user->type == cons('user.type.retailer')
                || (isset($search['action_type'])
                    && $search['action_type'] == 'sell')
            ) {//查询卖家
                $query->wherehas('shop', function ($q) use ($search) {
                    $q->where('name', trim($search['user_name']));
                });
            } else {//查询买家
                $query->wherehas('user.shop', function ($q) use ($search) {
                    $q->where('name', trim($search['user_name']));
                });
            }
        }
        //地址
        $query->wherehas('shop.shopAddress', function ($query) use ($search) {//根据店铺地址查询
            empty($search['province_id']) ?: $query->where('province_id', $search['province_id']);
            empty($search['city_id']) ?: $query->where('city_id', $search['city_id']);
            empty($search['district_id']) ?: $query->where('district_id', $search['district_id']);
        });

        return $query->nonCancel()->with('user.shop', 'shop', 'goods')->orderBy('id',
            'desc')->get();//第一个是买家的店铺,第二个是卖的店铺
    }

    /**
     * 订单和商品统计
     *
     * @param array $res
     * @return mixed
     */
    private function _orderStatistics($res)
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
                ++$stat['onlineNum'];
                $stat['onlineAmount'] += $value['price'];
            } else {
                ++$stat['codNum'];
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

        foreach (['user', 'seller', 'withdraw'] as $item) {
            $key = 'push:' . $item . ':' . $this->user->id;
            if ($redis->exists($key)) {
                $content = $redis->get($key);
                $redis->del($key);
                return response()->json(['type' => $item, 'data' => $content]);
            }
        }

        return response()->json([]);
    }

    /**
     * 导出订单统计,excel
     *
     * @param \Illuminate\Http\Request $request
     */
    public function postStatExport(Request $request)
    {
        //查询条件判断
        $search = $request->all();
        $stat = $this->_searchAllOrderByOptions($search);
        $otherStat = $this->_orderStatistics($stat);

        Excel::create('订单统计', function ($excel) use ($stat, $search, $otherStat) {

            $excel->sheet('sheet1', function ($sheet) use ($stat, $search, $otherStat) {
                // Set auto size for sheet
                $sheet->setAutoSize(true);

                //打印条件
                $options = $this->_spliceOptions($search);
                //订单信息统计
                $orderContent = $this->_spliceOrderContent($stat, $search['checkbox_flag']);
                //商品信息统计
                $goodsContent = $this->_spliceGoodsContent($otherStat['goods']);
                //订单汇总统计
                $orderStat = $this->_spliceOrderStat($otherStat['stat']);

                $out = array_merge($options, $orderContent, $goodsContent, $orderStat);

                $sheet->rows($out);

            });

        })->export('xls');
    }

    /**
     * 拼接过滤信息
     *
     * @param $search
     * @return array
     */
    private function _spliceOptions($search)
    {
        $options = [];
        if (!empty($search['start_at'])) {
            $options[] = ["开始时间:", $search['start_at']];
            if (!empty($search['end_at'])) {
                $options[] = ["结束时间:", $search['end_at']];
            }
        }
        empty($search['pay_type']) ?: $options[] = [
            "支付方式:",
            cons()->valueLang('pay_type')[$search['pay_type']]
        ];
        empty($search['obj_type']) ?: $options[] = [
            "订单对象:",
            cons()->valueLang('user.type')[$search['obj_type']]
        ];
        empty($search['goods_name']) ?: $options[] = ["商品名称:", $search['goods_name']];
        if (!empty($search['user_name'])) {
            $objOfShow = isset($search['obj_type']) ? $search['obj_type'] : 0;
            $showObjName = $this->_inputName($objOfShow);
            $options[] = [$showObjName . ":", $search['user_name']];
        }

        if (!empty($search['province_id'])) {
            $options[] = ['省市:', DB::table('address')->select('name')->find(intval($search['province_id']))];
        }
        if (!empty($search['city_id'])) {
            $options[] = ['城市:', DB::table('address')->select('name')->find(intval($search['city_id']))];
        }
        if (!empty($search['district_id'])) {
            $options[] = ['区县:', DB::table('address')->select('name')->find(intval($search['district_id']))];
        }

        return $options;
    }

    /**
     * 拼接订单统计详情
     *
     * @param $orders
     * @param $flag
     * @return array
     */
    private function _spliceOrderContent($orders, $flag)
    {
        $orderContent[] = ['订单号', '收件人', '支付方式', '订单状态', '创建时间', '订单金额'];
        if ($flag) {
            $orderContent[0] = array_merge($orderContent[0], ['商品编号', '商品名称', '商品单价', '商品数量']);
        }

        foreach ($orders as $order) {
            if ($flag) {
                foreach ($order['goods'] as $key => $value) {
                    if ($key) {
                        $orderContent[] = [
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $value['id'],
                            $value['name'],
                            '￥' . $value['pivot']['price'],
                            $value['pivot']['num']
                        ];
                    } else {
                        $orderContent[] = [
                            $order['id'],
                            $order['shippingAddress']['consigner'],
                            $order['payment_type'],
                            $order['status_name'],
                            $order['created_at'],
                            '￥' . $order['price'],
                            $value['id'],
                            $value['name'],
                            '￥' . $value['pivot']['price'],
                            $value['pivot']['num']
                        ];
                    }
                }
            } else {
                $orderContent[] = [
                    $order['id'],
                    $order['shippingAddress']['consigner'],
                    $order['payment_type'],
                    $order['status_name'],
                    $order['created_at'],
                    '￥' . $order['price'],
                ];
            }
        }

        return $orderContent;
    }

    /**
     * 拼接商品统计详情
     *
     * @param $goods
     * @return array
     */
    private function _spliceGoodsContent($goods)
    {
        $goodsContent[] = ['商品总计'];
        $goodsContent[] = ['商品编号', '商品名称', '平均单价', '商品数量', '商品支出金额'];

        foreach ($goods as $good) {
            $goodsContent[] = [$good['id'], $good['name'], $good['price'] / $good['num'], $good['num'], $good['price']];
        }

        return $goodsContent;
    }

    /**
     * 拼接订单汇总详情
     *
     * @param $stat
     * @return array
     */
    private function _spliceOrderStat($stat)
    {
        $res[] = ['订单总计'];
        $res[] = [
            '订单数',
            '总金额',
            '在线支付订单数',
            '在线支付订单总金额',
            '货到付款订单数',
            '货到付款总金额',
            '货到付款实收金额',
            '货到付款未收金额'
        ];
        $res[] = [
            $stat['totalNum'],
            $stat['totalAmount'],
            $stat['onlineNum'],
            $stat['onlineAmount'],
            $stat['codNum'],
            $stat['codAmount'],
            $stat['codReceiveAmount'],
            $stat['codAmount'] - $stat['codReceiveAmount']
        ];

        return $res;
    }

}
