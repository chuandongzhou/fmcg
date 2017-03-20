<?php

namespace App\Http\Controllers\Index;

use App\Services\CartService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Order;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Gate;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class OrderController extends Controller
{
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
            return in_array($key, (array)$goodsId);
        });

        if (empty($orderGoodsNum)) {
            return redirect()->back()->withInput();
        }
        $confirmedGoods = auth()->user()->carts()->whereIn('goods_id', array_keys($orderGoodsNum));

        $carts = $confirmedGoods->with('goods')->get();

        //验证
        $cartService = new CartService($carts);

        if (!$cartService->validateOrder($orderGoodsNum, true)) {
            return redirect()->back()->with('message', $cartService->getError());
        }

        $confirmedGoods->update(['status' => 1]);

        return redirect('order/confirm-order');

    }

    /**
     * 确认订单页
     *
     * @return \Illuminate\View\View
     */
    public function getConfirmOrder()
    {
        $user = auth()->user();
        $carts = $user->carts()->where('status', 1)->with('goods')->get();
        if ($carts->isEmpty()) {
            return redirect('cart')->with('message', '购物车为空');
        }
        $shops = (new CartService($carts))->formatCarts(null, true);


        //收货地址
        $shippingAddress = $user->shippingAddress()->with('address')->get();


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
        $data = $request->all();
        $orderService = new OrderService;

        $result = $orderService->orderSubmitHandle($data);
        if (!$result) {
            return redirect('cart')->with('message', $orderService->getError());
        }

        //$query = '?order_id=' . $result['order_id'] . ($result['type'] ? '&type=all' : '');
        $redirectUrl = url('order/finish-order' /*. $query*/);

        return redirect($redirectUrl);
    }

    /**
     * 订单提交完成页
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getFinishOrder(Request $request)
    {
//        $orderId = $request->input('order_id');
//
//        if (is_null($orderId)) {
//            return redirect(url('order-buy'));
//        }
//
//        $type = $request->input('type');
//        $field = $type == 'all' ? 'pid' : 'id';
//
//        $orders = Order::where($field, $orderId)->get([
//            'pay_type',
//            'pay_status',
//            'user_id',
//            'pay_way',
//            'is_cancel',
//            'price'
//        ]);
//        if (Gate::denies('validate-online-orders', $orders)) {
//            return redirect(url('order-buy'));
//        }
//        $balance = (new UserService())->getUserBalance();

        return view('index.order.finish-order'/*,
            [
                'orderId' => $orderId,
                'type' => $type,
                'userBalance' => $balance['availableBalance'],
                'orderSumPrice' => $orders->sum('price')
            ]*/);
    }

    /**
     * 支付成功
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPaySuccess()
    {
        return view('index.order.pay-success');
    }

    /**
     * 卖家订单统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatisticsOfSell(Request $request)
    {

        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_at', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_at', $carbon->copy()->toDateString());
        //买家名称
        $userShopName = array_get($data, 'user_shop_name');
        //支付方式
        $payType = array_get($data, 'pay_type');

        $orders = Order::ofSell(auth()->id())
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->ofUserShopName($userShopName)
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();

        $orderStatistics = $this->_groupOrderByTypeForStatistics($orders);

        return view('index.order.order-statistics-of-sell',
            array_merge(compact('startTime', 'endTime', 'userShopName', 'payType', 'data'), $orderStatistics));

    }

    /**
     * 按店铺查买家详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatisticsOfSellUserDetail(Request $request)
    {
        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_time', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_time', $carbon->copy()->toDateString());
        //买家名称
        $userId = array_get($data, 'user_id');
        //支付方式
        $payType = array_get($data, 'pay_type');


        $orders = Order::ofSell(auth()->id())
            ->ofBuy($userId)
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();

        extract($this->_groupOrderByType($orders));

        return view('index.order.order-statistics-of-sell-user-detail', [
            'orders' => $orders,
            'ownOrdersStatistics' => $this->_orderStatistics($ownOrders),
            'businessOrdersStatistics' => $this->_orderStatistics($businessOrders),
            'orderGoodsStatistics' => $this->_orderGoodsStatistics($orders)
        ]);

    }

    /**
     * 买家订单统计导出
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function getStatisticsOfSellExport(Request $request)
    {
        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_time', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_time', $carbon->copy()->toDateString());
        //买家名称
        $userShopName = array_get($data, 'user_shop_name');
        //支付方式
        $payType = array_get($data, 'pay_type');

        $user = auth()->user();

        $orders = Order::ofSell(auth()->id())
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->ofUserShopName($userShopName)
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();

        //dd($this->_orderGoodsStatistics($orders));

        $orderStatistics = $this->_groupOrderByTypeForStatistics($orders);

        $excelName = $startTime . '-' . $endTime . ' ' . $user->shop_name . '订单（出货）统计';

        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($orderStatistics) {
            $excel->sheet('订单总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {

                // Set auto size for sheet
                $sheet->setAutoSize(true);

                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 10,
                    'C' => 10,
                    'D' => 15,
                    'E' => 15,
                    'F' => 10,
                    'G' => 20,
                    'H' => 20,
                    'I' => 20,
                    'J' => 20,
                    'K' => 15,
                    'L' => 15,
                ));

                //标题
                $titles = [
                    '',
                    '订单数',
                    '总金额',
                    '实收金额',
                    '手续费',
                    '未收金额',
                    '在线支付订单数',
                    '在线支付金额',
                    '货到付款订单数',
                    '货到付款金额',
                    '自提订单数',
                    '自提订单金额'
                ];

                //自主订单
                $ownOrders = $orderStatistics['ownOrdersStatistics'];
                array_unshift($ownOrders, '自主订单');
                //业务订单
                $businessOrders = $orderStatistics['businessOrdersStatistics'];
                array_unshift($businessOrders, '业务订单');

                $total = ['合计'];

                foreach ($ownOrders as $key => $value) {
                    if ($key) {
                        $total[] = $value + $businessOrders[$key];
                    }
                }
                $sheet->rows([$titles, array_values($ownOrders), array_values($businessOrders), $total]);

                //单元格居中
                $sheet->cells('A1:L4', function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

                //打印条件
                /*$options = $this->_spliceOptions($search);

                //订单信息统计
                $orderInfo = $this->_spliceOrderContent($stat, isset($search['show_goods_name']));
                $orderContent = $orderInfo['orderContent'];
                $mergeArray = $orderInfo['mergeArray'];

                //商品信息统计
                $goodsContent = $this->_spliceGoodsContent($otherStat['goods']);

                //订单汇总统计
                $orderStat = $this->_spliceOrderStat($otherStat['stat']);


                $out = array_merge($options, $orderContent, $goodsContent, $orderStat);

                $sheet->rows($out);


                //总行数
                $rowCount = count($out);

                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 10,
                    'B' => 30,
                    'C' => 15,
                    'D' => 15,
                    'E' => 15,
                    'F' => 20,
                    'G' => 10,
                    'H' => 15,
                    'I' => 50,
                    'J' => 10,
                    'K' => 10,
                ));
                //单元格合并
                $sheet->setMergeColumn(array(
                    'columns' => array('A', 'B', 'C', 'D', 'E', 'F', 'G'),
                    'rows' => $mergeArray
                ));

                //单元格居中
                $sheet->cells('A1:K' . $rowCount, function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    // manipulate the range of cells
                });*/
            });
            $excel->sheet('客户总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {
                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 30,
                    'C' => 30,
                    'D' => 10,
                    'E' => 15,
                    'F' => 10,
                    'G' => 20,
                    'H' => 35,
                    'I' => 10,
                ));
                $titles = [
                    '客户名称',
                    '订单数(业务订单+自主订单)',
                    '总金额(业务订单+自主订单)',
                    '实收金额',
                    '手续费',
                    '未收金额',
                    '联系方式',
                    '地址',
                    '业务员'
                ];
                $shops = [];
                foreach ($orderStatistics['orderStatisticsGroupName'] as $item) {
                    $shops[] = [
                        $item['shopName'],
                        $item['orderCount'] . '(' . $item['businessOrderCount'] . '+' . $item['ownOrderCount'] . ')',
                        $item['amount'] . '(' . $item['businessOrderAmount'] . '+' . $item['ownOrderAmount'] . ')',
                        $item['actualAmount'],
                        $item['targetFee'],
                        $item['notPaidAmount'],
                        $item['contact'],
                        $item['address'],
                        $item['user_salesman']
                    ];
                }
                $sheet->rows(array_merge([$titles], $shops));
                //单元格居中
                $sheet->cells('A1:H' . (count($shops) + 1), function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
            $excel->sheet('商品总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {
                $this->_exportGoods($sheet, $orderStatistics);
            });
        })->export('xls');

    }

    /**
     * 买家订单统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatisticsOfBuy(Request $request)
    {

        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_time', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_time', $carbon->copy()->toDateString());
        //卖家名称
        $shopName = array_get($data, 'hop_name');
        //支付方式
        $payType = array_get($data, 'pay_type');

        $orders = Order::ofBuy(auth()->id())
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->ofShopName($shopName)
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();

        //dd($this->_orderGoodsStatistics($orders));

        $orderStatistics = $this->_groupOrderByTypeForStatistics($orders, false);

        return view('index.order.order-statistics-of-buy',
            array_merge(compact('startTime', 'endTime', 'shopName', 'payType', 'data'), $orderStatistics));

    }

    /**
     * 按店铺查卖家详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatisticsOfBuyUserDetail(Request $request)
    {
        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_time', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_time', $carbon->copy()->toDateString());
        //买家名称
        $shopId = array_get($data, 'shop_id');
        //支付方式
        $payType = array_get($data, 'pay_type');


        $orders = Order::ofBuy(auth()->id())
            ->where('shop_id', $shopId)
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();

        extract($this->_groupOrderByType($orders));

        return view('index.order.order-statistics-of-buy-user-detail', [
            'orders' => $orders,
            'ownOrdersStatistics' => $this->_orderStatistics($ownOrders),
            'businessOrdersStatistics' => $this->_orderStatistics($businessOrders),
            'orderGoodsStatistics' => $this->_orderGoodsStatistics($orders)
        ]);

    }

    /**
     * 买家订单统计导出
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function getStatisticsOfBuyExport(Request $request)
    {
        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startTime = array_get($data, 'start_time', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endTime = array_get($data, 'end_time', $carbon->copy()->toDateString());
        //卖家名称
        $shopName = array_get($data, 'hop_name');
        //支付方式
        $payType = array_get($data, 'pay_type');

        $user = auth()->user();

        $orders = Order::ofBuy(auth()->id())
            ->useful()
            ->ofPayType($payType)
            ->ofCreatedAt($startTime, (new Carbon($endTime))->endOfDay())
            ->ofShopName($shopName)
            ->with([
                'coupon',
                'salesmanVisitOrder.salesmanCustomer',
                'systemTradeInfo',
                'shop',
                'user.shop',
                'orderGoods.goods'
            ])
            ->get();



        $orderStatistics = $this->_groupOrderByTypeForStatistics($orders, false);

        $excelName = $startTime . '-' . $endTime . ' ' . $user->shop_name . '订单（进货）统计';

        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($orderStatistics) {
            $excel->sheet('订单总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {

                // Set auto size for sheet
                $sheet->setAutoSize(true);

                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 10,
                    'C' => 10,
                    'D' => 15,
                    'E' => 15,
                    'F' => 20,
                    'G' => 20,
                    'H' => 20,
                    'I' => 20,
                    'J' => 15,
                    'K' => 15,
                ));

                //标题
                $titles = [
                    '',
                    '订单数',
                    '总金额',
                    '已付金额',
                    '未付金额',
                    '在线支付订单数',
                    '在线支付金额',
                    '货到付款订单数',
                    '货到付款金额',
                    '自提订单数',
                    '自提订单金额'
                ];

                //自主订单
                $ownOrders = array_except($orderStatistics['ownOrdersStatistics'], 'targetFee');
                array_unshift($ownOrders, '自主订单');
                //业务订单
                $businessOrders = array_except($orderStatistics['businessOrdersStatistics'], 'targetFee');
                array_unshift($businessOrders, '业务订单');

                $total = ['合计'];

                foreach ($ownOrders as $key => $value) {
                    if ($key) {
                        $total[] = $value + $businessOrders[$key];
                    }
                }
                $sheet->rows([$titles, array_values($ownOrders), array_values($businessOrders), $total]);

                //单元格居中
                $sheet->cells('A1:K4', function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

            });
            $excel->sheet('店铺总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {
                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 30,
                    'C' => 30,
                    'D' => 10,
                    'E' => 15,
                    'F' => 20,
                    'G' => 35,
                    'H' => 10,
                ));
                $titles = [
                    '店铺名称',
                    '订单数(业务订单+自主订单)',
                    '总金额(业务订单+自主订单)',
                    '已付金额',
                    '未付金额',
                    '联系方式',
                    '地址',
                ];
                $shops = [];
                foreach ($orderStatistics['orderStatisticsGroupName'] as $item) {
                    $shops[] = [
                        $item['shopName'],
                        $item['orderCount'] . '(' . $item['businessOrderCount'] . '+' . $item['ownOrderCount'] . ')',
                        $item['amount'] . '(' . $item['businessOrderAmount'] . '+' . $item['ownOrderAmount'] . ')',
                        $item['actualAmount'],
                        $item['notPaidAmount'],
                        $item['contact'],
                        $item['address']
                    ];
                }
                $sheet->rows(array_merge([$titles], $shops));
                //单元格居中
                $sheet->cells('A1:H' . (count($shops) + 1), function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
            $excel->sheet('商品总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {
               $this->_exportGoods($sheet, $orderStatistics);
            });
        })->export('xls');

    }

    /**
     * 导出商品统计
     *
     * @param \Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param $orderStatistics
     */
    private function _exportGoods(LaravelExcelWorksheet $sheet, $orderStatistics){
        // 设置宽度
        $sheet->setWidth(array(
            'A' => 60,
            'B' => 10,
            'C' => 10,
            'D' => 15,
            'E' => 10
        ));
        $titles = [
            '商品名称',
            '总进货量',
            '总金额',
            '平均单价',
            '进货数量',
        ];
        $goods = [];
        //合并
        $mergeArray = [];
        foreach ($orderStatistics['orderGoodsStatistics'] as $item) {
            $start = count($goods) + 2;
            foreach ($item['pieces'] as $piece => $value) {
                if ($value == head($item['pieces'])) {
                    $mergeArray[$start] = [$start, $start];
                    $goods[] = [
                        $item['name'],
                        $item['num'],
                        $item['amount'],
                        bcdiv($value['amount'], $value['num'], 2) . '/' . cons()->valueLang('goods.pieces',
                            $piece),
                        $value['num']
                    ];
                } else {
                    $mergeArray[$start] = [$start, $start + 1];
                    $goods[] = [
                        '',
                        '',
                        '',
                        bcdiv($value['amount'], $value['num'], 2) . '/' . cons()->valueLang('goods.pieces',
                            $piece),
                        $value['num']
                    ];
                }

            }
        }
        $sheet->rows(array_merge([$titles], $goods));
        $sheet->setMergeColumn(array(
            'columns' => array('A','B','C'),
            'rows' => $mergeArray
        ));
        //单元格居中
        $sheet->cells('A1:E' . (count($goods) + 1), function (CellWriter $cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
    }

    /**
     * 订单总统计
     *
     * @param $orders
     * @param bool $isSeller
     * @return array
     */
    private function _groupOrderByTypeForStatistics(Collection $orders, $isSeller = true)
    {

        extract($this->_groupOrderByType($orders));
        return [
            'ownOrdersStatistics' => $this->_orderStatistics($ownOrders),
            'businessOrdersStatistics' => $this->_orderStatistics($businessOrders),
            'orderStatisticsGroupName' => $this->_orderStatisticsGroupName($orders, $isSeller),
            'orderGoodsStatistics' => $this->_orderGoodsStatistics($orders)
        ];
    }

    /**
     * 按订单类型拆分
     *
     * @param $orders
     * @return array
     */
    private function _groupOrderByType(Collection $orders)
    {

        $orderConf = cons('order');
        //自主订单
        $ownOrders = $orders->filter(function ($order) use ($orderConf) {
            return $order->type == $orderConf['type']['platform'];
        });

        //业务订单
        $businessOrders = $orders->reject(function ($order) use ($orderConf) {
            return $order->type == $orderConf['type']['platform'];
        });

        return compact('ownOrders', 'businessOrders');


    }

    /**
     * 订单统计
     *
     * @param $orders
     * @return array
     */
    private function _orderStatistics(Collection $orders)
    {
        $orderConf = cons('order');
        $payTypes = cons('pay_type');
        //已支付订单
        $paidOrders = $orders->filter(function ($order) use ($orderConf) {
            return $order->pay_status == $orderConf['pay_status']['payment_success'];
        });
        //未支付订单
        $notPaidOrders = $orders->reject(function ($order) use ($orderConf) {
            return $order->pay_status == $orderConf['pay_status']['payment_success'];
        });

        // 在线支付订单
        $onlinePayOrders = $orders->filter(function ($order) use ($payTypes) {
            return $order->pay_type == $payTypes['online'];
        });

        // 货到付款订单
        $codPayOrders = $orders->filter(function ($order) use ($payTypes) {
            return $order->pay_type == $payTypes['cod'];
        });

        // 自提订单
        $pickUpOrders = $orders->filter(function ($order) use ($payTypes) {
            return $order->pay_type == $payTypes['pick_up'];
        });

        return [
            'count' => $orders->count(),
            'amount' => $orders->sum('after_rebates_price'),
            'actualAmount' => $paidOrders->sum('actual_amount'),
            'targetFee' => $paidOrders->sum('target_fee'),
            'notPaidAmount' => $notPaidOrders->sum('after_rebates_price'),
            'onlinePayCount' => $onlinePayOrders->count(),
            'onlinePayAmount' => $onlinePayOrders->sum('after_rebates_price'),
            'codPayCount' => $codPayOrders->count(),
            'codPayAmount' => $codPayOrders->sum('after_rebates_price'),
            'pickUpCount' => $pickUpOrders->count(),
            'pickUpAmount' => $pickUpOrders->sum('after_rebates_price'),

        ];

    }

    /**
     * 订单按店铺名拆分
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @param $isSeller
     *
     * @return array
     */
    private function _orderStatisticsGroupName(Collection $orders, $isSeller = true)
    {
        $shopNameType = $isSeller ? 'user_shop_name' : 'shop_name';

        $shopNames = $orders->pluck($shopNameType)->toBase()->unique();
        $nameStatistics = [];
        foreach ($shopNames as $shopName) {
            $nameStatistics[$shopName] = $this->_orderStatisticsByShopName($orders, $shopName, $shopNameType);
        }
        return $nameStatistics;
    }

    /**
     * 按店铺名统计
     *
     * @param \Illuminate\Database\Eloquent\Collection $allOrders
     * @param $shopName
     * @param $shopNameType
     * @return array
     */
    private function _orderStatisticsByShopName(Collection $allOrders, $shopName, $shopNameType)
    {
        $orders = $allOrders->filter(function ($order) use ($shopName, $shopNameType) {
            return $order->{$shopNameType} == $shopName;
        });


        $firstOrder = $orders->first();
        if (empty($firstOrder)) {
            return [];
        }

        $isSeller = $shopNameType == 'user_shop_name';

        $orderStatistics = $this->_orderStatistics($orders);
        extract($this->_groupOrderByType($orders));

        $shopDetails = [
            'id' => $isSeller ? $firstOrder->user_id : $firstOrder->shop_id,
            'shopName' => $shopName,
            'contact' => $isSeller ? $firstOrder->user_contact . '-' . $firstOrder->user_contact_info : $firstOrder->shop_contact,
            'address' => $isSeller ? $firstOrder->user_shop_address : $firstOrder->shop_address,
            'orderCount' => $orders->count(),
            'businessOrderCount' => $businessOrders->count(),
            'businessOrderAmount' => $businessOrders->sum('after_rebates_price'),
            'ownOrderCount' => $ownOrders->count(),
            'ownOrderAmount' => $ownOrders->sum('after_rebates_price'),
        ];

        if ($isSeller) {
            $shopDetails['user_salesman'] = $firstOrder->user_salesman;
        }
        return array_merge($shopDetails, $orderStatistics);
    }

    /**
     * 订单商品统计
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @return array
     */
    private function _orderGoodsStatistics(Collection $orders)
    {

        $orderGoods = $orders->pluck('orderGoods')->collapse();

        $goodsStatistics = [];

        foreach ($orderGoods as $item) {
            $goodsId = $item->goods_id;
            $pieces = $item->pieces;
            !isset($goodsStatistics[$goodsId]['name']) && ($goodsStatistics[$goodsId]['name'] = $item->goods_name);

            $goodsStatistics[$goodsId]['num'] = isset($goodsStatistics[$goodsId]['num']) ? bcadd($goodsStatistics[$goodsId]['num'],
                $item->num) : $item->num;
            $goodsStatistics[$goodsId]['amount'] = isset($goodsStatistics[$goodsId]['amount']) ? bcadd($goodsStatistics[$goodsId]['amount'],
                $item->total_price) : $item->total_price;

            $goodsStatistics[$goodsId]['pieces'][$pieces] ['amount'] = isset($goodsStatistics[$goodsId]['pieces'][$pieces]['amount']) ? bcadd($goodsStatistics[$goodsId]['pieces'][$pieces]['amount'],
                $item->total_price) : $item->total_price;
            $goodsStatistics[$goodsId]['pieces'][$pieces] ['num'] = isset($goodsStatistics[$goodsId]['pieces'][$pieces]['num']) ? bcadd($goodsStatistics[$goodsId]['pieces'][$pieces]['num'],
                $item->num) : $item->num;
        }
        return $goodsStatistics;

    }
}
