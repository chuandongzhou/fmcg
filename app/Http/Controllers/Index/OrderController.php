<?php

namespace App\Http\Controllers\Index;

use App\Models\OrderGoods;
use App\Services\CartService;
use App\Services\ExportWordService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Order;

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
        })->whereIn('id', $orderIds)->where('pay_status', cons('order.pay_status.non_payment'))->nonSend()->update([
            'is_cancel' => cons('order.is_cancel.on'),
            'cancel_by' => $this->userId,
            'cancel_at' => Carbon::now()
        ]);
        if ($status) {
            return $this->success();
        }

        return $this->error('操作失败');
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

        $data = $request->input('shop');

        $payTypes = cons('pay_type');

        $onlinePaymentOrder = [];   //  保存在线支付的订单
        foreach ($shops as $shop) {
            $payType = array_get($payTypes, $data[$shop->id]['pay_type'], head($payTypes));
            $orderData = [
                'user_id' => auth()->user()->id,
                'shop_id' => $shop->id,
                'price' => $shop->sum_price,
                'pay_type' => $payType,
                //TODO: 需要验证收货地址是否合法
                'shipping_address_id' => $data[$shop->id]['shipping_address_id'],
                'remark' => $data[$shop->id]['remark'] ? $data[$shop->id]['remark'] : ''
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

        // TODO: 跳至支付页面
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
        $selectedObj = '';
        $selectedPay = '';
        //查询条件判断
        $search = $request->all();
        if (empty($search)) {
            //无查询条件时，查询该用户所有非取消订单。
            $stat = $this->_searchAllOrder();
        } else {
            $selectedObj = $search['obj_type'];
            $selectedPay = $search['pay_type'];
            $stat = $this->_searchAllOrderByOptions($search);
        }

        $otherStat = $this->_orderStatistics($stat['data']);

        return view('index.order.order-statistics', [
            'selectedPay' => $selectedPay,
            'selectedObj' => $selectedObj,
            'pay_type' => $payType,
            'obj_type' => $objType,
            'statistics' => $stat,
            'otherStat' => $otherStat
        ]);
    }

    /**
     * 搜索该用户参与的所有支付或发货的订单
     *
     * @return mixed
     */
    private function _searchAllOrder()
    {
        return Order::where(function ($query) {
            $query->where('user_id', $this->userId)->orWhere(function ($query) {
                $query->ofSellByShopId($this->userId);
            });
        })->where(function ($query) {
            //在线支付情况，只查询付款完成以后的状态
            $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                cons('order.pay_status.payment_success'));
        })->orWhere(function ($query) {
            //货到付款情况，只查询发货以后的状态
            $query->where('pay_type', cons('pay_type.cod'))->whereIn('status',
                [cons('order.status.send'), cons('order.status.finished')]);
        })->nonCancel()->with('user', 'shippingAddress', 'goods')->orderBy('id', 'desc')->paginate()->toArray();
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
            if ($search['pay_type'] == cons('pay_type.online')) {//在线支付
                $query->where('pay_type', cons('pay_type.online'))->where('pay_status',
                    cons('order.pay_status.payment_success'));
            } else { //货到付款
                $query->where('pay_type', cons('pay_type.cod'))->whereIn('status',
                    [cons('order.status.send'), cons('order.status.finished')]);
            }
            //查询对象
            if ($search['obj_type'] > $this->userId) {
                $query->ofSellByShopId(intval($search['obj_type']));
            } else {
                $query->where('user_id', intval($search['obj_type']));
            }

            if (!empty($search['start_at']) && !empty($search['end_at'])) {
                $query->whereBetween('created_at', [$search['start_at'], $search['end_at']]);
            }
        })->nonCancel()->with('user', 'shippingAddress', 'goods')->orderBy('id', 'desc')->paginate()->toArray();
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

                if (isset($goodStat[$good['id']])) {
                    $goodStat[$good['id']]['num'] += $num;
                    $goodStat[$good['id']]['price'] += $price;
                } else {

                    $goodStat[$good['id']] = ['name' => $name, 'price' => $price, 'num' => $num];
                }
            }
        }
        $stat['goods'] = $goodStat;

        return $stat;
    }

    /**
     * 导出订单word文档
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getExport(Request $request)
    {

        $orderIds = (array)$request->input('order_id');
        $res = Order::with('shippingAddress', 'goods', 'address')->where('user_id', $this->userId)->whereIn('id',
            $orderIds)->get()->toArray();
        if (empty($res)) {
            return $this->error('没有该订单信息');
        }
        $wordName = $this->_getFileName($res);

        return response()->download(public_path($wordName));

    }

    /**
     * 生成word文档
     *
     * @param $res
     * @return string
     */
    public function _getFileName($res)
    {
        $word = new ExportWordService();
        $word->start();
        foreach ($res as $item) {
            $html = ' <table style="width:600px;text-align: left;line-height: 30px">

                    <tr><th colspan="6" style="text-align: center;"><h1>送货单</h1></th></tr>
                    <tr><th>订单号:</th><td>' . $item['id'] . '</td></tr>';
            foreach ($item['goods'] as $good) {
                $html .= ' <tr><th>货号</th><th>商品名称</th><th>促销信息</th><th>数量</th><th>单价</th><th>小计</th></tr>
                    <tr><td>'
                    . $good['id']
                    . '</td><td>'
                    . $good['name']
                    . '</td><td>'
                    . $good['promotion_info']
                    . '</td><td>'
                    . $good['pivot']['num']
                    . '</td><td>'
                    . $good['pivot']['price']
                    . '</td><td>'
                    . $good['pivot']['total_price']
                    . '</td></tr>';
            }
            $html .= ' <tr><th>付款方式:</th><td colspan="5">'
                . $item['payment_type']
                . '</td></tr>
                    <tr><th>备注:</th><td colspan="5">'
                . $item['remark']
                . '</td></tr>
                    <tr><th colspan="5" style="text-align: right">合计:</th><td style="text-align: left">'
                . $item['price']
                . '</td></tr>
                    <tr style="height: 30px"></tr>
                    <tr><th>收货人:</th><td colspan="5">'
                . $item['shipping_address']['consigner']
                . '</td></tr>
                    <tr><th>电话:</th><td colspan="5">'
                . $item['shipping_address']['phone']
                . '</td></tr>
                    <tr><th>地址:</th><td colspan="5">'
                . $item['address']['detail_address']
                . '</td>

                    <tr><th colspan="6" style="text-align: right">'
                . date('Y-m-d')
                . '</th></tr>
                </table>
                ';
            echo $html;
        }
        $wordName = 'upload/' . strtotime('now') . rand() . '.doc';

        $word->save($wordName);

        return $wordName;
    }
}
