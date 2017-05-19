<?php

namespace App\Http\Controllers\ChildUser;


use App\Models\DeliveryMan;
use App\Services\OrderDownloadService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Order;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use QrCode;
use Excel;

class OrderController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = child_auth()->user();
        //卖家可执行功能列表
        //订单状态
        $orderStatus = cons()->lang('order.status');
        $payStatus = array_only(cons()->lang('order.pay_status'), ['refund', 'refund_success']);
        $orderStatus = array_merge( ['wait_receive' => '未收款'], $payStatus, $orderStatus);

        $search = $request->all();

        $orders = Order::ofSell($user->shop_id)->useful()->with([
            'user.shop',
            'shippingAddress.address',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ]);
        if (is_numeric($searchContent = array_get($search, 'search_content'))) {
            $orders = $orders->where('id', $searchContent);
        } elseif ($searchContent) {
            $orders = $orders->ofSelectOptions($search)->ofUserShopName($searchContent);
        } else {
            $orders = $orders->ofSelectOptions($search);
        }
        $deliveryMan = DeliveryMan::active()->where('shop_id', $user->shop()->pluck('id'))->lists('name',
            'id');

        return view('child-user.order.index', [
            'order_status' => $orderStatus,
            'data' => $this->_getOrderNum(),
            'orders' => $orders->orderBy('updated_at', 'desc')->paginate(),
            'delivery_man' => $deliveryMan,
            'search' => $search
        ]);
    }

    /**
     * 待发货订单
     */
    public function waitSend()
    {
        $orders = Order::ofSell(child_auth()->user()->shop_id)->useful()->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->nonSend();
        $deliveryMan = DeliveryMan::active()->where('shop_id', child_auth()->user()->shop()->pluck('id'))->lists('name',
            'id');
        return view('child-user.order.index', [
            'data' => $this->_getOrderNum($orders->count()),
            'orders' => $orders->paginate(),
            'delivery_man' => $deliveryMan
        ]);
    }

    /**
     * 待收款订单
     */
    public function waitReceive()
    {
        $orders = Order::ofSell(child_auth()->user()->shop_id)->getPayment()->useful()->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ]);
        return view('child-user.order.index', [
            'data' => $this->_getOrderNum(-1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }

    /**
     * 待确认订单
     */
    public function getWaitConfirm()
    {
        $orders = Order::ofSell(child_auth()->user()->shop_id)->with([
            'user.shop',
            'goods' => function ($query) {
                $query->select(['goods.id', 'goods.name', 'goods.bar_code'])->wherePivot('type',
                    cons('order.goods.type.order_goods'));
            }
        ])->waitConfirm();
        return view('child-user.order.index', [
            'data' => $this->_getOrderNum(-1, -1, $orders->count()),
            'orders' => $orders->paginate()
        ]);
    }


    /**
     * 查询订单详情
     *
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function show($orderId)
    {

        $order = Order::ofSell(child_auth()->user()->shop_id)->useful()->with('user.shop', 'shop.user', 'goods',
            'shippingAddress.address', 'systemTradeInfo',
            'orderChangeRecode', 'gifts')->find($orderId);

        if (!$order) {
            return redirect('child-user/order');
        }

        $diffTime = Carbon::now()->diffInSeconds($order->updated_at);

        $goods = (new OrderService)->explodeOrderGoods($order);
        $goods['orderGoods']->each(function ($goods) use (&$goods_quantity) {
            $goods_quantity += $goods->pivot->num;
        });
        $deliveryMan = DeliveryMan::where('shop_id', child_auth()->user()->shop_id)->lists('name', 'id');

        return view('child-user.order.detail', [
            'order' => $order,
            'goods_quantity' => $goods_quantity,
            'mortgageGoods' => $goods['mortgageGoods'],
            'orderGoods' => $goods['orderGoods'],
            'delivery_man' => $deliveryMan,
            'backUrl' => $diffTime > 10 ? 'javascript:history.back()' : url('order-sell')
        ]);
    }

    /**
     * 选择模版页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function templete()
    {
        $shop = child_auth()->user()->shop;

        $defaultTempleteId = app('order.download')->getTemplete($shop->id);

        $tempHeaders = $shop->orderTempletes;

        return view('child-user.order.templete',
            ['defaultTempleteId' => $defaultTempleteId, 'tempHeaders' => $tempHeaders]);
    }

    /**
     * 导出订单word文档,只有卖家可以导出
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function export(Request $request)
    {
        $orderIds = (array)$request->input('order_id');
        if (empty($orderIds)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $result = Order::with('shippingAddress.address', 'goods', 'shop')
            ->ofSell(child_auth()->user()->shop_id)->useful()
            ->whereIn('id', $orderIds)->get();
        if ($result->isEmpty()) {
            return $this->error('要导出的订单不存在', null, ['export_error' => '要导出的订单不存在']);
        }
        (new OrderDownloadService())->download($result);
    }

    /**
     * 查询浏览器打印订单详情
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function getBrowserExport(Request $request)
    {
        $orderId = $request->input('order_id');
        $templeteId = $request->input('templete_id', 0);
        if (empty($orderId)) {
            return $this->error('请选择要导出的订单', null, ['export_error' => '请选择要导出的订单']);
        }

        $order = Order::with('shippingAddress.address', 'shop', 'user', 'gifts')
            ->ofSell(child_auth()->user()->shop_id)->useful()
            ->find($orderId);
        if (is_null($order)) {
            return $this->error('订单不存在');
        }

        $orderGoods = (new OrderService())->explodeOrderGoods($order);

        $allNum = 0;
        foreach ($orderGoods['orderGoods'] as $goods) {
            $allNum += $goods->pivot->num;
        }
        $order->allNum = $allNum;
        $shopId = $order->shop_id;
        $modelId = app('order.download')->getTemplete($shopId);
        (new OrderDownloadService())->addDownloadCount($order);

        if ($templete = $order->shop->orderTempletes()->find($templeteId)) {
            $order->shop = $templete;
        }


        return view('index.order.sell.templet.templet-table' . $modelId,
            [
                'order' => $order,
                'orderGoods' => $orderGoods['orderGoods'],
                'mortgageGoods' => $orderGoods['mortgageGoods']
            ]);

    }

    /**
     * 卖家订单统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function statistics(Request $request)
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

        $orders = Order::ofSell(child_auth()->user()->shop_id)
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

        return view('child-user.order.statistics',
            array_merge(compact('startTime', 'endTime', 'userShopName', 'payType', 'data'), $orderStatistics));

    }
    /**
     * 买家订单统计导出
     *
     * @param \Illuminate\Http\Request $request
     *
     */
    public function statisticsExport(Request $request)
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

        $user = child_auth()->user();

        $orders = Order::ofSell($user->shop_id)
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

        $excelName = $startTime . '-' . $endTime . ' ' . $user->shop_name . '订单（出货）统计';

        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($orderStatistics) {
            $excel->sheet('订单总计', function (LaravelExcelWorksheet $sheet) use ($orderStatistics) {
                $this->_exportOrder($sheet, $orderStatistics);
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
                        $item['address']->address_name,
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
     * 获取不同状态订单数
     *
     * @param int $nonSend
     * @param int $waitReceive
     * @param int $waitConfirm
     * @return array
     */
    private function _getOrderNum($nonSend = -1, $waitReceive = -1, $waitConfirm = -1)
    {
        $shopId = child_auth()->user()->shop_id;
        $data = [
            'nonSend' => $nonSend >= 0 ? $nonSend : Order::OfSell($shopId)->nonSend()->count(),
            //待发货
            'waitReceive' => $waitReceive >= 0 ? $waitReceive : Order::OfSell($shopId)->getPayment()->useful()->count(),
            //待收款（针对货到付款）
            'waitConfirm' => $waitConfirm >= 0 ? $waitConfirm : Order::OfSell($shopId)->waitConfirm()->useful()->count(),
            //待确认
        ];


        return $data;
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
            'ownOrdersStatistics' => $this->_orderStatistics($ownOrders, $isSeller),
            'businessOrdersStatistics' => $this->_orderStatistics($businessOrders, $isSeller),
            'orderStatisticsGroupName' => $this->_orderStatisticsGroupName($orders, $isSeller),
            'orderGoodsStatistics' => $this->_orderGoodsStatistics($orders)
        ];
    }

    /**
     * 订单统计
     *
     * @param $orders
     * @param  bool $isSeller
     * @return array
     */
    private function _orderStatistics(Collection $orders, $isSeller = true)
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

        $result = [
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

        return $isSeller ? $result : array_except($result, 'targetFee');

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
     * 导出订单总计
     *
     * @param \Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param $orderStatistics
     * @param bool $isSeller
     */
    private function _exportOrder(LaravelExcelWorksheet $sheet, $orderStatistics, $isSeller = true)
    {

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
        if ($isSeller) {
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
        } else {
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
        }

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
    }

    /**
     * 导出商品统计
     *
     * @param \Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet
     * @param $orderStatistics
     * @param bool $isSeller
     */
    private function _exportGoods(LaravelExcelWorksheet $sheet, $orderStatistics, $isSeller = true)
    {
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
            $isSeller ? '总出货量' : '总进货量',
            '总金额',
            '平均单价',
            $isSeller ? '出货数量' : '进货数量',
        ];
        $goods = [];
        //合并
        $mergeArray = [];
        foreach ($orderStatistics['orderGoodsStatistics'] as $item) {
            $start = count($goods) + 2;
            foreach ($item['pieces'] as $piece => $value) {
                if ($piece == key($item['pieces'])) {
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
            'columns' => array('A', 'B', 'C'),
            'rows' => $mergeArray
        ));
        //单元格居中
        $sheet->cells('A1:E' . (count($goods) + 1), function (CellWriter $cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
        });
    }

}
