<?php

namespace App\Http\Controllers\Admin;

use App\Models\DataStatistics;
use App\Models\Goods;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;

class OperationDataController extends Controller
{
    /**
     * 用户
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user(Request $request)
    {
        $now = Carbon::now();
        $beginDay = $request->input('begin_day', $now->copy()->startOfMonth()->format('Y-m-d'));
        $endDay = $request->input('end_day', $now->format('Y-m-d'));

        //用户数据
        return view('admin.operation.user', $this->_getUserData($beginDay, $endDay));
    }

    /**
     * 导出用户数据
     *
     * @param \Illuminate\Http\Request $request
     */
    public function userExport(Request $request)
    {
        $beginDay = $request->input('begin_day', Carbon::now()->format('Y-m-d'));
        $endDay = $request->input('end_day', $beginDay);


        Excel::create('用户数据统计', function ($excel) use ($beginDay, $endDay) {
            $excel->sheet('Excel sheet', function ($sheet) use ($beginDay, $endDay) {
                extract($this->_getUserData($beginDay, $endDay));
                $sheet->setWidth(array(
                    'A' => 20,
                    'B' => 20,
                    'C' => 20,
                    'D' => 20,
                    'E' => 20,
                ));

                $sheet->row(1, ['名称', '供应商', '批发商', '终端商', '总计'])->setStyle([
                    'alignment' => ['vertical' => 'center']
                ]);


                $sheet->row(2, [
                    '注册数',
                    $supplierRegNum = $dataStatistics->sum('supplier_reg_num'),
                    $wholesalerRegNum = $dataStatistics->sum('wholesaler_reg_num'),
                    $retailerRegNum = $dataStatistics->sum('retailer_reg_num'),
                    $supplierRegNum + $wholesalerRegNum + $retailerRegNum
                ]);
                $sheet->row(3, [
                    '活跃用户数',
                    $activeUser ? $activeUser->active_user[0] . '(' . $activeUser->created_at . ')' : 0,
                    $activeUser ? $activeUser->active_user[1] . '(' . $activeUser->created_at . ')' : 0,
                    $activeUser ? $activeUser->active_user[2] . '(' . $activeUser->created_at . ')' : 0,
                    $activeUser ? array_sum($activeUser->active_user) : 0
                ]);
                $sheet->row(4, [
                    '下单用户数',
                    ' - - ',
                    $orderGroup['wholesalerOrders']->pluck('user_id')->toBase()->unique()->count(),
                    $orderGroup['retailerOrders']->pluck('user_id')->toBase()->unique()->count(),
                    ' - - '
                ]);
                $sheet->row(5, [
                    '成单用户数',
                    ' - - ',
                    $completeOrderGroup['wholesalerOrders']->pluck('user_id')->toBase()->unique()->count(),
                    $completeOrderGroup['retailerOrders']->pluck('user_id')->toBase()->unique()->count(),
                    ' - - '
                ]);
            });
        })->export('xls');

    }

    /**
     * 金融
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function financial(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);

        $beginDay = $date['beginDay'];
        $dayEnd = (new Carbon($date['endDay']))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'systemTradeInfo', 'shop'];

        // 下单统计
        $orders = Order::whereBetween('created_at', [$beginDay, $dayEnd])
            ->where('is_cancel', cons('order.is_cancel.off'))
            ->where('status', '<>', cons('order.status.invalid'))
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name']);
            });

        $data = array_merge([
            'beginDay' => $date['beginDay'],
            'endDay' => $date['endDay'],
            'data' => $data
        ], $this->_getFinancial($orders));

        return view('admin.operation.financial', $data);
    }

    /**
     * 金融数据导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function financialExport(Request $request)
    {
        $date = $this->_formatDate($request->all());

        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($date['endDay']))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'systemTradeInfo'];

        // 下单统计
        $orders = Order::whereBetween('created_at', [$beginDay, $dayEnd])
            ->where('is_cancel', cons('order.is_cancel.off'))
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name']);
            });

        extract($this->_getFinancial($orders));

        // Creating the new document...
        $phpWord = new PhpWord();
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $titles = [
            '' => 1000,
            '渠道' => 1200,
            '下单总笔数' => 1800,
            '下单总金额' => 1800,
            '需支付金额' => 1600,
            '已完成支付金额' => 2200,
            '在线支付金额' => 2200,
            '现金支付金额' => 2200,
            '未完成支付金额' => 2200,
        ];
        $table = $this->_getTable($phpWord, $titles);

        //买家金融统计数据
        $data = [
            //终端商
            [
                '终端商' => $cellRowSpan,
                '自主订单',
                number_format($retailerOwnerCount = $retailer['owner']['count']),
                number_format($retailerOwnerAmount = $retailer['owner']['amount'], 2),
                number_format($retailerOwnerAfterRebates = $retailer['owner']['afterRebates'], 2),
                number_format($retailerOwnerCompleteAmount = $retailer['owner']['completeAmount'], 2),
                number_format($retailerOwnerCompleteAmountByOnline = $retailer['owner']['completeAmountByOnline'], 2),
                number_format($retailerOwnerCompleteAmountByOffline = bcsub($retailerOwnerCompleteAmount,
                    $retailerOwnerCompleteAmountByOnline, 2), 2),
                number_format($retailerOwnerNotAmount = bcsub($retailerOwnerAfterRebates, $retailerOwnerCompleteAmount,
                    2), 2),
            ],
            [
                '' => $cellRowContinue,
                '业务订单',
                number_format($retailerBusinessCount = $retailer['business']['count']),
                number_format($retailerBusinessAmount = $retailer['business']['amount'], 2),
                number_format($retailerBusinessAfterRebates = $retailer['business']['afterRebates'], 2),
                number_format($retailerBusinessCompleteAmount = $retailer['business']['completeAmount'], 2),
                number_format($retailerBusinessCompleteAmountByOnline = $retailer['business']['completeAmountByOnline'],
                    2),
                number_format($retailerBusinessCompleteAmountByOffline = bcsub($retailerBusinessCompleteAmount,
                    $retailerBusinessCompleteAmountByOnline, 2), 2),
                number_format($retailerBusinessNotAmount = bcsub($retailerBusinessAfterRebates,
                    $retailerBusinessCompleteAmount, 2), 2),
            ],
            [
                '' => $cellRowContinue,
                '自提订单',
                number_format($retailerPickUpCount = $retailer['pickUp']['count']),
                number_format($retailerPickUpAmount = $retailer['pickUp']['amount'], 2),
                number_format($retailerPickUpAfterRebates = $retailer['pickUp']['afterRebates'], 2),
                number_format($retailerPickUpCompleteAmount = $retailer['pickUp']['completeAmount'], 2),
                number_format($retailerPickUpCompleteAmountByOnline = $retailer['pickUp']['completeAmountByOnline'], 2),
                number_format($retailerPickUpCompleteAmountByOffline = bcsub($retailerPickUpCompleteAmount,
                    $retailerPickUpCompleteAmountByOnline, 2), 2),
                number_format($retailerPickUpNotAmount = bcsub($retailerPickUpAfterRebates,
                    $retailerPickUpCompleteAmount, 2), 2),
            ],
            [
                '' => $cellRowContinue,
                '总计',
                number_format($retailerCount = $retailerOwnerCount + $retailerBusinessCount + $retailerPickUpCount),
                number_format($retailerAmount = $retailerOwnerAmount + $retailerBusinessAmount + $retailerPickUpAmount,
                    2),
                number_format($retailerAfterRebates = $retailerOwnerAfterRebates + $retailerBusinessAfterRebates + $retailerPickUpAfterRebates,
                    2),
                number_format($retailerCompleteAmount = $retailerOwnerCompleteAmount + $retailerBusinessCompleteAmount + $retailerPickUpCompleteAmount,
                    2),
                number_format($retailerCompleteAmountByOnline = $retailerOwnerCompleteAmountByOnline + $retailerPickUpCompleteAmountByOnline + $retailerPickUpCompleteAmountByOnline,
                    2),
                number_format($retailerCompleteAmountByOffline = $retailerOwnerCompleteAmountByOffline + $retailerBusinessCompleteAmountByOffline + $retailerPickUpCompleteAmountByOffline,
                    2),
                number_format($retailerCompleteNotAmount = $retailerOwnerNotAmount + $retailerBusinessNotAmount + $retailerPickUpNotAmount,
                    2),
            ],
            //批发商
            [
                '批发' => $cellRowSpan,
                '自主订单',
                number_format($wholesalerOwnerCount = $wholesaler['owner']['count']),
                number_format($wholesalerOwnerAmount = $wholesaler['owner']['amount'], 2),
                number_format($wholesalerOwnerAfterRebates = $wholesaler['owner']['afterRebates'], 2),
                number_format($wholesalerOwnerCompleteAmount = $wholesaler['owner']['completeAmount'], 2),
                number_format($wholesalerOwnerCompleteAmountByOnline = $wholesaler['owner']['completeAmountByOnline'],
                    2),
                number_format($wholesalerOwnerCompleteAmountByOffline = bcsub($wholesalerOwnerCompleteAmount,
                    $wholesalerOwnerCompleteAmountByOnline, 2), 2),
                number_format($wholesalerOwnerNotAmount = bcsub($wholesalerOwnerAfterRebates,
                    $wholesalerOwnerCompleteAmount, 2), 2),
            ],
            [
                '' => $cellRowContinue,
                '业务订单',
                number_format($wholesalerBusinessCount = $wholesaler['business']['count']),
                number_format($wholesalerBusinessAmount = $wholesaler['business']['amount'], 2),
                number_format($wholesalerBusinessAfterRebates = $wholesaler['business']['afterRebates'], 2),
                number_format($wholesalerBusinessCompleteAmount = $wholesaler['business']['completeAmount'], 2),
                number_format($wholesalerBusinessCompleteAmountByOnline = $wholesaler['business']['completeAmountByOnline'],
                    2),
                number_format($wholesalerBusinessCompleteAmountByOffline = bcsub($wholesalerBusinessCompleteAmount,
                    $wholesalerBusinessCompleteAmountByOnline, 2), 2),
                number_format($wholesalerBusinessNotAmount = bcsub($wholesalerBusinessAfterRebates,
                    $wholesalerBusinessCompleteAmount, 2), 2),
            ],
            [
                '' => $cellRowContinue,
                '自提订单',
                number_format($wholesalerPickUpCount = $wholesaler['pickUp']['count']),
                number_format($wholesalerPickUpAmount = $wholesaler['pickUp']['amount'], 2),
                number_format($wholesalerPickUpAfterRebates = $wholesaler['pickUp']['afterRebates'], 2),
                number_format($wholesalerPickUpCompleteAmount = $wholesaler['pickUp']['completeAmount'], 2),
                number_format($wholesalerPickUpCompleteAmountByOnline = $wholesaler['pickUp']['completeAmountByOnline'],
                    2),
                number_format($wholesalerPickUpCompleteAmountByOffline = bcsub($wholesalerPickUpCompleteAmount,
                    $wholesalerPickUpCompleteAmountByOnline, 2), 2),
                number_format($wholesalerPickUpNotAmount = bcsub($wholesalerPickUpAfterRebates,
                    $wholesalerPickUpCompleteAmount, 2), 2),
            ],
            [
                '' => $cellRowContinue,
                '总计',
                number_format($wholesalerCount = $wholesalerOwnerCount + $wholesalerBusinessCount + $wholesalerPickUpCount),
                number_format($wholesalerAmount = $wholesalerOwnerAmount + $wholesalerBusinessAmount + $wholesalerPickUpAmount,
                    2),
                number_format($wholesalerAfterRebates = $wholesalerOwnerAfterRebates + $wholesalerBusinessAfterRebates + $wholesalerPickUpAfterRebates,
                    2),
                number_format($wholesalerCompleteAmount = $wholesalerOwnerCompleteAmount + $wholesalerBusinessCompleteAmount + $wholesalerPickUpCompleteAmount,
                    2),
                number_format($wholesalerCompleteAmountByOnline = $wholesalerOwnerCompleteAmountByOnline + $wholesalerPickUpCompleteAmountByOnline + $wholesalerPickUpCompleteAmountByOnline,
                    2),
                number_format($wholesalerCompleteAmountByOffline = $wholesalerOwnerCompleteAmountByOffline + $wholesalerBusinessCompleteAmountByOffline + $wholesalerPickUpCompleteAmountByOffline,
                    2),
                number_format($wholesalerCompleteNotAmount = $wholesalerOwnerNotAmount + $wholesalerBusinessNotAmount + $wholesalerPickUpNotAmount,
                    2),
            ],
            //总计
            [
                '合计' => ['valign' => 'center', 'gridSpan' => 2],
                number_format($retailerCount + $wholesalerCount),
                number_format($retailerAmount + $wholesalerAmount, 2),
                number_format($retailerAfterRebates + $wholesalerAfterRebates, 2),
                number_format($retailerCompleteAmount + $wholesalerCompleteAmount, 2),
                number_format($retailerCompleteAmountByOnline + $wholesalerCompleteAmountByOnline, 2),
                number_format($retailerCompleteAmountByOffline + $wholesalerCompleteAmountByOffline, 2),
                number_format($retailerCompleteNotAmount + $wholesalerCompleteNotAmount, 2)
            ]
        ];
        $this->_fillData($table, $data);
        $sellerTitles = [
            '' => 1000,
            '渠道' => 1000,
            '已完成总笔数' => 1800,
            '已完成总金额' => 1800,
            '在线支付金额' => 1800,
            '现金支付金额' => 1800,
            '未完成总金额' => 2200,
            '未收款总金额' => 2200,
            '未配送总金额' => 2200,
        ];
        $sellerTable = $this->_getTable($phpWord, $sellerTitles);
        //卖家金融统计数据
        $sellerData = [
            [
                '供应商' => $cellRowSpan,
                '终端商',
                number_format($supplierByRetailerCompleteCount = $supplierByRetailer['completeCount']),
                number_format($supplierByRetailerCompleteAmount = $supplierByRetailer['completeAmount'], 2),
                number_format($supplierByRetailerCompleteAmountByOnlinePay = $supplierByRetailer['completeAmountByOnlinePay'],
                    2),
                number_format($supplierByRetailerCompleteAmountByOffPay = $supplierByRetailerCompleteAmount - $supplierByRetailerCompleteAmountByOnlinePay,
                    2),
                number_format($supplierByRetailerNotCompleteAmount = $supplierByRetailer['notCompleteAmount'], 2),
                number_format($supplierByRetailerNotReceiveAmount = $supplierByRetailer['notReceiveAmount'], 2),
                number_format($supplierByRetailerNotDeliveryAmount = $supplierByRetailer['notDeliveryAmount'], 2),
            ],
            [
                '' => $cellRowContinue,
                '批发商',
                number_format($supplierByWholesalerCompleteCount = $supplierByWholesaler['completeCount']),
                number_format($supplierByWholesalerCompleteAmount = $supplierByWholesaler['completeAmount'], 2),
                number_format($supplierByWholesalerCompleteAmountByOnlinePay = $supplierByWholesaler['completeAmountByOnlinePay'],
                    2),
                number_format($supplierByWholesalerCompleteAmountByOffPay = $supplierByWholesalerCompleteAmount - $supplierByWholesalerCompleteAmountByOnlinePay,
                    2),
                number_format($supplierByWholesalerNotCompleteAmount = $supplierByWholesaler['notCompleteAmount'], 2),
                number_format($supplierByWholesalerNotReceiveAmount = $supplierByWholesaler['notReceiveAmount'], 2),
                number_format($supplierByWholesalerNotDeliveryAmount = $supplierByWholesaler['notDeliveryAmount'], 2),
            ],
            [
                '' => $cellRowContinue,
                '总计',
                number_format($supplierCount = $supplierByRetailerCompleteCount + $supplierByWholesalerCompleteCount),
                number_format($supplierAmount = $supplierByRetailerCompleteAmount + $supplierByWholesalerCompleteAmount,
                    2),
                number_format($supplierOnlineAmount = $supplierByRetailerCompleteAmountByOnlinePay + $supplierByWholesalerCompleteAmountByOnlinePay,
                    2),
                number_format($supplierOfflineAmount = $supplierByRetailerCompleteAmountByOffPay + $supplierByWholesalerCompleteAmountByOffPay,
                    2),
                number_format($supplierNotCompleteAmount = $supplierByRetailerNotCompleteAmount + $supplierByWholesalerNotCompleteAmount,
                    2),
                number_format($supplierNotReceiveAmount = $supplierByRetailerNotReceiveAmount + $supplierByWholesalerNotReceiveAmount,
                    2),
                number_format($supplierNotDeliveryAmount = $supplierByRetailerNotDeliveryAmount + $supplierByWholesalerNotDeliveryAmount,
                    2),
            ],
            [
                '批发商',
                '终端商',
                number_format($wholesalerByRetailerCompleteCount = $wholesalerByRetailer['completeCount']),
                number_format($wholesalerByRetailerCompleteAmount = $wholesalerByRetailer['completeAmount'], 2),
                number_format($wholesalerByRetailerCompleteAmountByOnlinePay = $wholesalerByRetailer['completeAmountByOnlinePay'],
                    2),
                number_format($wholesalerByRetailerCompleteAmountByOffPay = $wholesalerByRetailerCompleteAmount - $wholesalerByRetailerCompleteAmountByOnlinePay,
                    2),
                number_format($wholesalerByRetailerNotCompleteAmount = $wholesalerByRetailer['notCompleteAmount'], 2),
                number_format($wholesalerByRetailerNotReceiveAmount = $wholesalerByRetailer['notReceiveAmount'], 2),
                number_format($wholesalerByRetailerNotDeliveryAmount = $wholesalerByRetailer['notDeliveryAmount'], 2),
            ],
            [
                '合计' => ['valign' => 'center', 'gridSpan' => 2],
                number_format($supplierCount + $wholesalerByRetailerCompleteCount),
                number_format($supplierAmount + $wholesalerByRetailerCompleteAmount, 2),
                number_format($supplierOnlineAmount + $wholesalerByRetailerCompleteAmountByOnlinePay, 2),
                number_format($supplierOfflineAmount + $wholesalerByRetailerCompleteAmountByOffPay, 2),
                number_format($supplierNotCompleteAmount + $wholesalerByRetailerNotCompleteAmount, 2),
                number_format($supplierNotReceiveAmount + $wholesalerByRetailerNotReceiveAmount, 2),
                number_format($supplierNotDeliveryAmount + $wholesalerByRetailerNotDeliveryAmount, 2),
            ]
        ];
        $this->_fillData($sellerTable, $sellerData);
        $name = $beginDay . '至' . $endDay . '金融数据统计.docx';
        $phpWord->save($name, 'Word2007', true);
    }

    /**
     * 下单金额统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orderAmount(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);

        $name = array_get($data, 'name');
        $beginDay = $date['beginDay'];
        $dayEnd = (new Carbon($date['endDay']))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'systemTradeInfo', 'coupon'];

        // 下单统计
        $orders = Order::whereBetween('created_at', [$beginDay, $dayEnd])
            ->where('is_cancel', cons('order.is_cancel.off'))
            ->where('status', '<>', cons('order.status.invalid'))
            ->ofUserShopName($name)
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name', 'user_shop_name']);
                $order->setHidden(['salesman_visit_order']);
            });
        $shopGroup = $this->_groupOrdersByName($orders);


        $result = $this->_getOrdersData($orders);

        return view('admin.operation.order-amount', [
            'name' => $name,
            'beginDay' => $beginDay,
            'endDay' => $date['endDay'],
            'retailer' => $result['retailer'],
            'wholesaler' => $result['wholesaler'],
            'shopGroup' => $shopGroup,
            'data' => $data
        ]);
    }

    /**
     * 下单金额统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orderAmountExport(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);

        $name = array_get($data, 'name');
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'systemTradeInfo'];

        // 下单统计
        $orders = Order::whereBetween('created_at', [$beginDay, $dayEnd])
            ->where('is_cancel', cons('order.is_cancel.off'))
            ->ofUserShopName($name)
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name', 'user_shop_name']);
                $order->setHidden(['salesman_visit_order']);
            });
        $shopGroup = $this->_groupOrdersByName($orders);

        $result = $this->_getOrdersData($orders);
        $retailer = $result['retailer'];
        $wholesaler = $result['wholesaler'];

        // Creating the new document...
        $phpWord = new PhpWord();
        $cellAlignCenter = array('align' => 'center');

        $titles = [
            '名称' => 1000,
            '下单笔数' => 1200,
            '下单总金额' => 2000,
            '在线支付金额(元)' => 2500,
            '线下支付金额(元)' => 2500,
            '需支付金额(元)' => 2500,
            '已完成支付金额(元)' => 2500,
            '未完成支付金额(元)' => 2500,
        ];

        $table = $this->_getTable($phpWord, $titles);

        $data = [
            [
                '终端商',
                $retailerOrderCount = $retailer['orderCount'],
                number_format($retailerOrderAmount = $retailer['orderAmount'], 2),
                number_format($retailerOrderPaidByOnline = $retailer['orderPaidByOnline'], 2),
                number_format($retailerOrderPaidByOffline = $retailer['orderPaidByOffline'], 2),
                number_format($retailerOrderRebatesAmount = $retailer['orderRebatesAmount'], 2),
                number_format($retailerPaidSuccess = $retailer['paidSuccess'], 2),
                number_format($retailerNotPaid = bcsub($retailerOrderRebatesAmount, $retailerPaidSuccess, 2), 2),
            ],
            [
                '批发商',
                $wholesalerOrderCount = $wholesaler['orderCount'],
                number_format($wholesalerOrderAmount = $wholesaler['orderAmount'], 2),
                number_format($wholesalerOrderPaidByOnline = $wholesaler['orderPaidByOnline'], 2),
                number_format($wholesalerOrderPaidByOffline = $wholesaler['orderPaidByOffline'], 2),

                number_format($wholesalerOrderRebatesAmount = $wholesaler['orderRebatesAmount'], 2),

                number_format($wholesalerPaidSuccess = $wholesaler['paidSuccess'], 2),
                number_format($wholesalerNotPaid = bcsub($wholesalerOrderRebatesAmount, $wholesalerPaidSuccess, 2), 2),
            ],
            [
                '总计',
                bcadd($retailerOrderCount, $wholesalerOrderCount),
                bcadd($retailerOrderAmount, $wholesalerOrderAmount, 2),
                bcadd($retailerOrderPaidByOnline, $wholesalerOrderPaidByOnline, 2),
                bcadd($retailerOrderPaidByOffline, $wholesalerOrderPaidByOffline, 2),
                bcadd($retailerOrderRebatesAmount, $wholesalerOrderRebatesAmount, 2),
                bcadd($retailerPaidSuccess, $wholesalerPaidSuccess, 2),
                bcadd($retailerNotPaid, $wholesalerNotPaid, 2),
            ]
        ];

        $this->_fillData($table, $data);

        $titles1 = [
            '购买商名称' => 3000,
            '下单笔数' => 1000,
            '下单总金额(元)' => 1800,
            '在线支付金额(元)' => 1800,
            '线下支付金额(元)' => 1800,
            '需支付金额(元)' => 1800,
            '已完成支付金额(元)' => 2400,
            '未完成支付金额(元)' => 2400,
        ];
        $table1 = $this->_getTable($phpWord, $titles1);

        foreach ($shopGroup as $shopName => $shop) {
            $table1->addRow();
            $table1->addCell()->addText($shopName . '(' . cons()->lang('user.type')[$shop['type']] . ')', null,
                $cellAlignCenter);
            $table1->addCell()->addText(array_get($shop, 'orderCount', 0), null, $cellAlignCenter);
            $table1->addCell()->addText(number_format($orderAmount = array_get($shop, 'orderAmount', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell()->addText(number_format($onlinePay = array_get($shop, 'onLinePay', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell()->addText(number_format($offlinePay = array_get($shop, 'offLinePay', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell()->addText(number_format($orderRebatesAmount = array_get($shop, 'orderRebatesAmount', 0),
                2), null,
                $cellAlignCenter);
            $table1->addCell()->addText(number_format($paySuccess = array_get($shop, 'paySuccess', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell()->addText(number_format(bcsub($orderRebatesAmount, $paySuccess, 2), 2), null,
                $cellAlignCenter);
        }

        $name = $beginDay . '至' . $endDay . '下单金额统计.docx';
        $phpWord->save($name, 'Word2007', true);

    }

    /**
     * 成交金额统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completeAmount(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);

        $name = array_get($data, 'name');
        $beginDay = $date['beginDay'];
        $dayEnd = (new Carbon($date['endDay']))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'shop', 'systemTradeInfo', 'coupon'];

        // 下单统计
        $orders = Order::whereBetween('finished_at', [$beginDay, $dayEnd])
            ->ofShopName($name)
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name', 'shop_name']);
                $order->setHidden(['salesman_visit_order']);
            });

        $shopGroup = $this->_groupCompleteOrdersByName($orders, false);


        $result = $this->_getCompleteOrderData($orders);

        return view('admin.operation.complete-amount', [
            'name' => $name,
            'beginDay' => $beginDay,
            'endDay' => $date['endDay'],
            'supplier' => $result['supplier'],
            'wholesaler' => $result['wholesaler'],
            'shopGroup' => $shopGroup,
            'data' => $data
        ]);

    }

    /**
     * 成交金额导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function completeAmountExport(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);

        $name = array_get($data, 'name');
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($date['endDay']))->endOfDay();
        $with = ['user', 'salesmanVisitOrder.salesmanCustomer', 'shop', 'systemTradeInfo', 'coupon'];

        // 下单统计
        $orders = Order::whereBetween('finished_at', [$beginDay, $dayEnd])
            ->ofShopName($name)
            ->with($with)
            ->get()
            ->each(function ($order) {
                $order->setAppends(['user_type_name', 'shop_name']);
                $order->setHidden(['salesman_visit_order']);
            });

        $shopGroup = $this->_groupOrdersByName($orders, false);
        $result = $this->_getCompleteOrderData($orders);
        $supplier = $result['supplier'];
        $wholesaler = $result['wholesaler'];

        // Creating the new document...
        $phpWord = new PhpWord();
        $cellAlignCenter = array('align' => 'center');
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');

        $titles = [
            '名称' => 1000,
            '成交订单笔数' => 2000,
            '成交总金额（元）' => 2000,
            '在线收款金额（元）' => 2500,
            '线下收款金额（元）' => 2500,
            'pos收款金额（元）' => 3000,
        ];

        $table = $this->_getTable($phpWord, $titles);

        $data = [
            [
                '供应商' => $cellRowSpan,
                $supplierForWholesalerCount = $supplier['wholesaler']['count'] . '（批发）',
                number_format($supplierForWholesalerAmount = $supplier['wholesaler']['amount'], 2) . '（批发）',
                number_format($supplierForWholesalerOnline = $supplier['wholesaler']['onlineAmount'], 2) . '（批发）',
                number_format($supplierForWholesalerOffline = $supplier['wholesaler']['offAmount'], 2) . '（批发）',
                number_format($supplierForWholesalerPos = $supplier['wholesaler']['posAmount'], 2) . '（批发）',
            ],
            [
                '' => $cellRowContinue,
                $supplierForRetailerCount = $supplier['retailer']['count'] . '（终端）',
                number_format($supplierForRetailerAmount = $supplier['retailer']['amount'], 2) . '（终端）',
                number_format($supplierForRetailerOnline = $supplier['retailer']['onlineAmount'], 2) . '（终端）',
                number_format($supplierForRetailerOffline = $supplier['retailer']['offAmount'], 2) . '（终端）',
                number_format($supplierForRetailerPos = $supplier['retailer']['posAmount'], 2) . '（终端）',
            ],
            [
                '批发商',
                $retailerOrderCount = $wholesaler['count'],
                number_format($retailerOrderAmount = $wholesaler['amount'], 2),
                number_format($retailerByOnline = $wholesaler['onlineAmount'], 2),
                number_format($retailerByOffline = $wholesaler['offAmount'], 2),
                number_format($retailerByPos = $wholesaler['posAmount'], 2),
            ],
            [
                '总计',
                number_format($supplierForWholesalerCount + $supplierForRetailerCount + $retailerOrderCount),
                number_format($supplierForWholesalerAmount + $supplierForRetailerAmount + $retailerOrderAmount, 2),
                number_format($supplierForWholesalerOnline + $supplierForRetailerOnline + $retailerByOnline, 2),
                number_format($supplierForWholesalerOffline + $supplierForRetailerOffline + $retailerByOffline, 2),
                number_format($supplierForWholesalerPos + $supplierForRetailerPos + $retailerByPos, 2),
            ]
        ];

        $this->_fillData($table, $data);

        $titles1 = [
            '出售商名称' => 3300,
            '成交笔数' => 1200,
            '成交总金额(元)' => 2200,
            '在线收款金额(元)' => 2400,
            '线下收款金额(元)' => 2400,
            'pos收款金额(元)' => 2400,
        ];

        $table1 = $this->_getTable($phpWord, $titles1);

        foreach ($shopGroup as $shopName => $shop) {
            $table1->addRow();
            $table1->addCell(0)->addText($shopName . '(' . cons()->lang('user.type')[$shop['type']] . ')', null,
                $cellAlignCenter);
            $table1->addCell(0)->addText(array_get($shop, 'orderCount', 0), null, $cellAlignCenter);
            $table1->addCell(0)->addText(number_format(array_get($shop, 'orderAmount', 0), 2), null, $cellAlignCenter);
            $table1->addCell(0)->addText(number_format(array_get($shop, 'onLinePay', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell(0)->addText(number_format(array_get($shop, 'offLinePay', 0), 2), null,
                $cellAlignCenter);
            $table1->addCell(0)->addText(number_format(array_get($shop, 'posPay', 0), 2), null, $cellAlignCenter);
        }


        $name = $beginDay . '至' . $endDay . '成交金额统计.docx';
        $phpWord->save($name, 'Word2007', true);

    }

    /**
     * 下单图
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function orderCreateMap(Request $request)
    {
        $data = $request->all();
        $beginDay = array_get($data, 'begin_day', Carbon::now()->format('Y-m-d'));
        $endDay = array_get($data, 'end_day', $beginDay);
        $dayEnd = (new Carbon($endDay))->endOfDay();

        $orders = Order::whereBetween('created_at', [$beginDay, $dayEnd])
            ->where('is_cancel', cons('order.is_cancel.off'))->with([
                'user',
                'salesmanVisitOrder.salesmanCustomer'
            ])
            ->where('status', '<>', cons('order.status.invalid'))
            ->get();
        $result = $this->_formatOrders($orders, $beginDay, $endDay);
        return $this->success($result);

    }

    /**
     * 销售排行
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function salesRank(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();
        $result = $this->_getRankData($data, $beginDay, $dayEnd);
        return view('admin.operation.sales-rank', array_merge($result, compact('beginDay', 'endDay', 'data')));

        //商品销售
    }

    /**
     * 销售排行导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function salesRankExport(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();
        $result = $this->_getRankData($data, $beginDay, $dayEnd);

        // Creating the new document...
        $phpWord = new PhpWord();
        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $cellAlignCenter = array('align' => 'center');
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        $section = $phpWord->addSection($style);
        $table = $section->addTable('table');

        $titles = [
            '排序' => 1200,
            '商品名称' => 5000,
            '所属分类' => 5000,
            '销售金额(元)' => 3000,
        ];

        $table->addRow();
        $table->addCell(0, ['valign' => 'center', 'gridSpan' => 4])->addText('前十商品销售金额排位', ['bold' => true],
            $cellAlignCenter);


        $table->addRow();
        foreach ($titles as $name => $width) {
            $table->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true], $cellAlignCenter);
        }
        $orderGoods = $result['orderGoods'];
        $goods = $result['goods'];
        $shops = $result['shops'];
        $shopAmount = $result['shopAmount'];

        foreach ($orderGoods as $key => $item) {
            $table->addRow();
            $table->addCell(0)->addText($key + 1, null, $cellAlignCenter);
            $table->addCell(0)->addText(str_limit($goods[$item->goods_id]->name, 30), null, $cellAlignCenter);
            $table->addCell(0)->addText($goods[$item->goods_id]->category_name, null, $cellAlignCenter);
            $table->addCell(0)->addText($item->amount, null, $cellAlignCenter);
        }

        $section1 = $phpWord->addSection($style);
        $table1 = $section1->addTable('table');
        $table1->addRow();
        $table1->addCell(0, ['valign' => 'center', 'gridSpan' => 4])->addText('前十商铺销售金额排位', ['bold' => true],
            $cellAlignCenter);

        $titles1 = [
            '排序' => 1200,
            '店铺名称' => 5000,
            '店铺类型' => 5000,
            '销售金额(元)' => 3000,
        ];

        $table1->addRow();
        foreach ($titles1 as $name => $width) {
            $table1->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true], $cellAlignCenter);
        }
        foreach ($shopAmount as $key => $item) {
            $table1->addRow();
            $table1->addCell(0)->addText($key + 1, null, $cellAlignCenter);
            $table1->addCell(0)->addText($shops[$item->shop_id]->name, null, $cellAlignCenter);
            $table1->addCell(0)->addText(cons()->valueLang('user.type', $shops[$item->shop_id]->user_type), null,
                $cellAlignCenter);
            $table1->addCell(0)->addText($item->amount, null, $cellAlignCenter);
        }
        $name = $beginDay . '至' . $endDay . '销售排行.docx';
        $phpWord->save($name, 'Word2007', true);
    }

    /**
     * 商品销售统计
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function goodsSales(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();

        $result = $this->_getGoodsSalesData($data, $beginDay, $dayEnd);

        $orderGoods = $result['orderGoods'];
        $goods = $result['goods'];

        //$orderGoods = $this->paginate($orderGoods);

        return view('admin.operation.goods-sales',
            compact('orderGoods', 'goods', 'beginDay', 'endDay', 'data'));
    }

    /**
     * 商品销售导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function goodsSalesExport(Request $request)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();

        $result = $this->_getGoodsSalesData($data, $beginDay, $dayEnd);

        $orderGoods = $result['orderGoods'];
        $goods = $result['goods'];

        // Creating the new document...
        $phpWord = new PhpWord();
        $cellAlignCenter = array('align' => 'center');

        $titles = [
            '商品ID' => 1200,
            '商品条形码' => 2000,
            '商品名称' => 3000,
            '所属分类' => 3000,
            '店铺名' => 4000,
            '销售量' => 1000,
            '销售金额(元)' => 1500,
        ];

        $table = $this->_getTable($phpWord, $titles);

        foreach ($orderGoods as $item) {
            $table->addRow();
            $table->addCell()->addText($goodsId = $item->goods_id, null, $cellAlignCenter);
            $table->addCell()->addText($goods[$goodsId]->bar_code, null, $cellAlignCenter);
            $table->addCell()->addText(str_limit($goods[$goodsId]->name, 20), null, $cellAlignCenter);
            $table->addCell()->addText($goods[$goodsId]->category_name, null, $cellAlignCenter);
            $table->addCell()->addText($goods[$goodsId]->shop_name . '(' . cons()->valueLang('user.type',
                    $goods[$goodsId]->user_type) . ')', null, $cellAlignCenter);
            $table->addCell()->addText($item->count, null, $cellAlignCenter);
            $table->addCell()->addText(number_format($item->amount, 2), null, $cellAlignCenter);
        }


        $name = $beginDay . '至' . $endDay . '商品销售详情.docx';
        $phpWord->save($name, 'Word2007', true);

    }

    /**
     * 商品销售数据
     *
     * @param \Illuminate\Http\Request $request
     * @param $goodsId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function goodsSalesMap(Request $request, $goodsId)
    {
        $data = $request->all();
        $date = $this->_formatDate($data);
        $beginDay = $date['beginDay'];
        $endDay = $date['endDay'];
        $dayEnd = (new Carbon($endDay))->endOfDay();
        $orderConfig = cons('order');

        $orderGoods = OrderGoods::with('goods')->whereHas('order', function ($query) use ($orderConfig) {
            $query->where('is_cancel', $orderConfig['is_cancel']['off'])
                ->where('status', '<>', $orderConfig['status']['invalid']);
        })->where('goods_id', $goodsId)->whereBetween('created_at',
            [$beginDay, $dayEnd])->get();


        $result = $this->_formatOrderGoods($orderGoods, $beginDay, $endDay);

        $result = array_merge($result, ['goods' => $orderGoods->first()->goods]);

        return $this->success($result);
    }

    /**
     * 获取用户注册数
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userRegister(Request $request)
    {
        $beginDay = $request->input('begin_day', Carbon::now()->format('Y-m-d'));
        $endDay = $request->input('end_day', $beginDay);

        $regUser = DataStatistics::whereBetween('created_at', [$beginDay, $endDay])->get();

        return $this->success([
            'created_at' => $regUser->pluck('created_at'),
            'retailer_reg' => $regUser->pluck('retailer_reg_num'),
            'wholesaler_reg' => $regUser->pluck('wholesaler_reg_num'),
            'supplier_reg' => $regUser->pluck('supplier_reg_num'),
            'retailer_login' => $regUser->pluck('retailer_login_num'),
            'wholesaler_login' => $regUser->pluck('wholesaler_login_num'),
            'supplier_login' => $regUser->pluck('supplier_login_num'),
        ]);

    }

    /**
     * 获取用户数据
     *
     * @param $beginDay
     * @param $endDay
     * @return array
     */
    private function _getUserData($beginDay, $endDay)
    {

        $dataStatistics = DataStatistics::whereBetween('created_at', [$beginDay, $endDay])->get();

        //活跃用户数
        $activeUser = $dataStatistics->last();


        $dayEnd = (new Carbon($endDay))->endOfDay();
        //下单
        $orders = Order::with('user', 'salesmanVisitOrder')->whereBetween('created_at', [$beginDay, $dayEnd])->get();

        $orderGroup = $this->_orderGroupByBuyer($orders);

        //成单
        $completeOrders = Order::with('user', 'salesmanVisitOrder')->whereBetween('finished_at',
            [$beginDay, $dayEnd])->get();
        $completeOrderGroup = $this->_orderGroupByBuyer($completeOrders);
        return compact('beginDay', 'endDay', 'activeUser', 'orderGroup', 'completeOrderGroup', 'dataStatistics');
    }

    /**
     * 格式化时间
     *
     * @param $data
     * @return array
     */
    private function _formatDate($data)
    {
        if ($time = array_get($data, 't')) {
            $timeInterval = $this->_getTimeInterval($time);
            $beginDay = $timeInterval['beginDay'];
            $endDay = $timeInterval['endDay'];
        } else {
            $now = Carbon::now();
            $beginDay = array_get($data, 'begin_day', $now->copy()->startOfMonth()->format('Y-m-d'));
            $endDay = array_get($data, 'end_day', $now->format('Y-m-d'));
        }

        return compact('beginDay', 'endDay');
    }

    /**
     * 获取开始和结束时间
     *
     * @param $time
     * @return array
     */
    private function _getTimeInterval($time)
    {
        switch ($time) {
            case 'today' :
                $beginDay = Carbon::now()->format('Y-m-d');
                $endDay = $beginDay;
                break;
            case 'yesterday' :
                $beginDay = Carbon::yesterday()->format('Y-m-d');
                $endDay = $beginDay;
                break;
            case 'week' :
                $beginDay = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endDay = Carbon::now()->format('Y-m-d');
                break;
            case 'month' :
                $beginDay = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDay = Carbon::now()->format('Y-m-d');
                break;
            default :
                $beginDay = Carbon::now()->format('Y-m-d');
                $endDay = $beginDay;
        }
        return compact('beginDay', 'endDay');
    }

    /**
     * 获取订单
     *
     * @param $orders
     * @return array
     */
    private function _getOrdersData($orders)
    {
        //终端商下单
        $retailerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'retailer';
        });

        //批发商下单
        $wholesalerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'wholesaler';
        });

        return [
            'retailer' => array_merge($this->_getOrdersGroupByChannel($retailerOrders), [
                'orderCount' => $retailerOrders->count(),
                'orderAmount' => $retailerOrders->sum('price'),
                'orderRebatesAmount' => $retailerOrders->sum('after_rebates_price'),
            ]),
            'wholesaler' => array_merge($this->_getOrdersGroupByChannel($wholesalerOrders), [
                'orderCount' => $wholesalerOrders->count(),
                'orderAmount' => $wholesalerOrders->sum('price'),
                'orderRebatesAmount' => $wholesalerOrders->sum('after_rebates_price'),
            ])
        ];
    }

    /**
     * 订单按渠道分组
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getOrdersGroupByChannel(Collection $orders)
    {
        $payStatus = cons('order.pay_status');
        $orderPayType = cons('pay_type');
        //终端商线上支付单
        $online = $orders->filter(function ($item) use ($orderPayType) {
            return $item->pay_type == $orderPayType['online'];
        });

        //终端商线下支付单
        $offline = $orders->reject(function ($item) use ($orderPayType) {
            return $item->pay_type == $orderPayType['online'];
        });

        //终端商已完成支付
        $paidSuccess = $orders->filter(function ($item) use ($payStatus) {
            return $item->pay_status == $payStatus['payment_success'];
        });

        return [
            'orderPaidByOnline' => $online->sum('price'),
            'orderPaidByOffline' => $offline->sum('price'),
            'paidSuccess' => $paidSuccess->sum('after_rebates_price')
        ];
    }

    /**
     * 获取金融数据
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getFinancial(Collection $orders)
    {
        //终端商下单
        $retailerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'retailer';
        });

        //批发商下单
        $wholesalerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'wholesaler';
        });

        //订单按卖家渠道分组
        $group = $this->_orderGroupBySeller($orders);

        return [
            'retailer' => $this->_getFinancialGroupByChannel($retailerOrders),
            'wholesaler' => $this->_getFinancialGroupByChannel($wholesalerOrders),
            'wholesalerByRetailer' => $this->_getFinancialForSeller($group['wholesalerOrders']),
            'supplierByWholesaler' => $this->_getFinancialForSeller($group['supplierOrdersBuyByWholesaler']),
            'supplierByRetailer' => $this->_getFinancialForSeller($group['supplierOrdersBuyByRetailer']),
        ];

    }

    /**
     * 金融数据按渠道分组
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getFinancialGroupByChannel(Collection $orders)
    {
        $orderPayType = cons('pay_type');
        //自主订单
        $ownerOrders = $orders->filter(function ($item) use ($orderPayType) {
            return $item->type == 0 && $item->pay_type != $orderPayType['pick_up'];
        });

        //业务订单

        $businessOrders = $orders->filter(function ($item) use ($orderPayType) {
            return $item->type == 1;
        });

        //自提订单

        $pickUpOrders = $orders->filter(function ($item) use ($orderPayType) {
            return $item->pay_type == $orderPayType['pick_up'];
        });


        return [
            'owner' => $this->_getFinancialDetail($ownerOrders),
            'business' => $this->_getFinancialDetail($businessOrders),
            'pickUp' => $this->_getFinancialDetail($pickUpOrders),
        ];

    }

    /**
     * 获取金融数据详情
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getFinancialDetail(Collection $orders)
    {
        $payStatus = cons('order.pay_status');
        //已完成的订单
        $completeOrders = $orders->filter(function ($item) use ($payStatus) {
            return $item->pay_status == $payStatus['payment_success'];
        });

        return [
            'count' => $orders->count(),
            'amount' => $orders->sum('price'),
            'afterRebates' => $orders->sum('after_rebates_price'),
            'completeAmount' => $completeOrders->sum('after_rebates_price'),
            'completeAmountByOnline' => $completeOrders->filter(function ($item) {
                return !is_null($item->systemTradeInfo);
            })->sum('after_rebates_price'),
        ];
    }

    /**
     * 卖家金融数据
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getFinancialForSeller(Collection $orders)
    {
        $orderStatus = cons('order.status');
        $payStatus = cons('order.pay_status');
        //已完成订单
        $completeOrders = $orders->filter(function ($item) use ($orderStatus) {
            return $item->status == $orderStatus['finished'];
        });

        //已完成在线支付
        $completeOrdersByOnlinePay = $completeOrders->filter(function ($item) use ($orderStatus) {
            return !is_null($item->systemTradeInfo);
        });

        //未完成订单
        $notCompleteOrders = $orders->filter(function ($item) use ($orderStatus) {
            return $item->status > $orderStatus['non_confirm'] && $item->status < $orderStatus['finished'];
        });
        //未收款订单
        $notReceiveOrders = $notCompleteOrders->filter(function ($item) use ($payStatus) {
            return $item->pay_status == $payStatus['non_payment'];
        });

        //未配送订单
        $notDeliveryOrders = $notCompleteOrders->filter(function ($item) use ($orderStatus) {
            return $item->status == $orderStatus['non_send'];
        });

        return [
            'completeCount' => $completeOrders->count(),
            'completeAmount' => $completeOrders->sum('after_rebates_price'),
            'completeAmountByOnlinePay' => $completeOrdersByOnlinePay->sum('after_rebates_price'),
            'notCompleteAmount' => $notCompleteOrders->sum('after_rebates_price'),
            'notReceiveAmount' => $notReceiveOrders->sum('after_rebates_price'),
            'notDeliveryAmount' => $notDeliveryOrders->sum('after_rebates_price'),
        ];

    }

    /**
     * 格式化
     *
     * @param $orders
     * @param $beginDay
     * @param $endDay
     * @return array
     */
    private function _formatOrders($orders, $beginDay, $endDay)
    {
        extract($this->_orderGroupByBuyer($orders));

        $beginDayCarbon = new Carbon($beginDay);
        $endDayCarbon = new Carbon($endDay);

        $diffDays = $beginDayCarbon->diffInDays($endDayCarbon);

        $retailerAmount = [];
        $wholesalerAmount = [];

        if ($diffDays == 0) {
            $dates = list_in_hours($beginDayCarbon, $endDayCarbon->endOfDay());
            foreach ($dates as $hour) {
                $index = explode(' ', $hour)[1] . ':00';
                $retailerAmount[$index] = $retailerOrders->filter(function ($order) use ($hour) {
                    return $order->created_at->format('Y-m-d H') == $hour;
                })->sum('price');
                $wholesalerAmount[$index] = $wholesalerOrders->filter(function ($order) use ($hour) {
                    return $order->created_at->format('Y-m-d H') == $hour;
                })->sum('price');
            }

        } elseif ($diffDays <= 31) {
            $dates = list_in_days($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $day) {
                $retailerAmount[$day] = $retailerOrders->filter(function ($order) use ($day) {
                    return $order->created_at->format('Y-m-d') == $day;
                })->sum('price');
                $wholesalerAmount[$day] = $wholesalerOrders->filter(function ($order) use ($day) {
                    return $order->created_at->format('Y-m-d') == $day;
                })->sum('price');
            }
        } elseif ($diffDays <= 366) {
            $dates = list_in_months($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $month) {
                $retailerAmount[$month] = $retailerOrders->filter(function ($order) use ($month) {
                    return $order->created_at->format('Y-m') == $month;
                })->sum('price');
                $wholesalerAmount[$month] = $wholesalerOrders->filter(function ($order) use ($month) {
                    return $order->created_at->format('Y-m') == $month;
                })->sum('price');
            }
        } else {
            $dates = list_in_years($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $year) {
                $retailerAmount[$year] = $retailerOrders->filter(function ($order) use ($year) {
                    return $order->created_at->format('Y') == $year;
                })->sum('price');
                $wholesalerAmount[$year] = $wholesalerOrders->filter(function ($order) use ($year) {
                    return $order->created_at->format('Y') == $year;
                })->sum('price');
            }
        }
        return [
            'dates' => array_keys($retailerAmount),
            'retailerAmount' => array_values($retailerAmount),
            'wholesalerAmount' => array_values($wholesalerAmount)
        ];

    }

    /**
     * 格式化订单商品
     *
     * @param $orderGoods
     * @param $beginDay
     * @param $endDay
     * @return array
     */
    private function _formatOrderGoods($orderGoods, $beginDay, $endDay)
    {
        $beginDayCarbon = new Carbon($beginDay);
        $endDayCarbon = new Carbon($endDay);

        $diffDays = $beginDayCarbon->diffInDays($endDayCarbon);

        $orderGoodsList = [];

        if ($diffDays == 0) {
            $dates = list_in_hours($beginDayCarbon, $endDayCarbon->endOfDay());
            foreach ($dates as $hour) {
                $index = explode(' ', $hour)[1] . ':00';
                $orderGoodsList[$index] = $orderGoods->filter(function ($goods) use ($hour) {
                    return $goods->created_at->format('Y-m-d H') == $hour;
                })->sum('total_price');
            }

        } elseif ($diffDays <= 31) {
            $dates = list_in_days($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $day) {
                $orderGoodsList[$day] = $orderGoods->filter(function ($goods) use ($day) {
                    return $goods->created_at->format('Y-m-d') == $day;
                })->sum('total_price');
            }
        } elseif ($diffDays <= 366) {
            $dates = list_in_months($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $month) {
                $orderGoodsList[$month] = $orderGoods->filter(function ($goods) use ($month) {
                    return $goods->created_at->format('Y-m') == $month;
                })->sum('total_price');
            }
        } else {
            $dates = list_in_years($beginDayCarbon, $endDayCarbon);
            foreach ($dates as $year) {
                $orderGoodsList[$year] = $orderGoods->filter(function ($goods) use ($year) {
                    return $goods->created_at->format('Y') == $year;
                })->sum('total_price');
            }
        }
        return [
            'dates' => array_keys($orderGoodsList),
            'orderGoodsList' => array_values($orderGoodsList),
        ];

    }

    /**
     * 按店铺名分组
     *
     * @param $orders
     * @param $buyer '是否买家'
     * @return array
     */
    private function _groupOrdersByName($orders, $buyer = true)
    {
        $shop = [];
        foreach ($orders as $order) {
            $name = $buyer ? $order->user_shop_name : $order->shop_name;
            $typeName = $buyer ? $order->user_type_name : array_search($order->shop_user_type, cons('user.type'));
            $shop[$name]['type'] = $typeName;
            $shop[$name]['orderCount'] = isset($shop[$name]['orderCount']) ? ++$shop[$name]['orderCount'] : 1;
            $shop[$name]['orderAmount'] = isset($shop[$name]['orderAmount']) ? bcadd($shop[$name]['orderAmount'],
                $order->price, 2) : $order->price;

            $shop[$name]['orderRebatesAmount'] = isset($shop[$name]['orderRebatesAmount']) ? bcadd($shop[$name]['orderRebatesAmount'],
                $order->after_rebates_price, 2) : $order->after_rebates_price;

            if (!is_null($order->systemTradeInfo) && $order->systemTradeInfo->pay_type != $order['pos']) {
                $shop[$name]['onLinePay'] = isset($shop[$name]['onLinePay']) ? bcadd($shop[$name]['onLinePay'],
                    $order->price, 2) : $order->price;
            } else {
                $shop[$name]['offLinePay'] = isset($shop[$name]['offLinePay']) ? bcadd($shop[$name]['offLinePay'],
                    $order->price, 2) : $order->price;
            }

            if ($order->pay_status == cons('order.pay_status.payment_success')) {
                $shop[$name]['paySuccess'] = isset($shop[$name]['paySuccess']) ? bcadd($shop[$name]['paySuccess'],
                    $order->after_rebates_price, 2) : $order->after_rebates_price;
            }

        }
        return $shop;
    }

    /**
     * 按店铺分组
     *
     * @param $orders
     * @param $buyer '是否买家'
     * @return array
     */
    private function _groupCompleteOrdersByName($orders, $buyer = true)
    {
        $shop = [];
        foreach ($orders as $order) {
            $name = $buyer ? $order->user_shop_name : $order->shop_name;
            $typeName = $buyer ? $order->user_type_name : array_search($order->shop_user_type, cons('user.type'));
            $shop[$name]['type'] = $typeName;
            $shop[$name]['orderCount'] = isset($shop[$name]['orderCount']) ? ++$shop[$name]['orderCount'] : 1;
            $shop[$name]['orderAmount'] = isset($shop[$name]['orderAmount']) ? bcadd($shop[$name]['orderAmount'],
                $order->after_rebates_price, 2) : $order->after_rebates_price;

            if (is_null($order->systemTradeInfo)) {
                $shop[$name]['offLinePay'] = isset($shop[$name]['offLinePay']) ? bcadd($shop[$name]['offLinePay'],
                    $order->after_rebates_price, 2) : $order->after_rebates_price;
            } elseif (!is_null($order->systemTradeInfo) && $order->systemTradeInfo->pay_type == $order['pos']) {
                $shop[$name]['posPay'] = isset($shop[$name]['posPay']) ? bcadd($shop[$name]['posPay'],
                    $order->after_rebates_price, 2) : $order->after_rebates_price;
            } else {
                $shop[$name]['onLinePay'] = isset($shop[$name]['onLinePay']) ? bcadd($shop[$name]['onLinePay'],
                    $order->after_rebates_price, 2) : $order->after_rebates_price;
            }
        }
        return $shop;
    }

    /**
     * 获取完成订单数据
     *
     * @param $orders
     * @return array
     */
    private function _getCompleteOrderData($orders)
    {
        extract($this->_orderGroupBySeller($orders));

        $wholesaler = $this->_getCompleteOrderDetail($wholesalerOrders);
        $supplier = [
            'retailer' => $this->_getCompleteOrderDetail($supplierOrdersBuyByRetailer),
            'wholesaler' => $this->_getCompleteOrderDetail($supplierOrdersBuyByWholesaler),
        ];
        return compact('wholesaler', 'supplier');
    }

    /**
     * 获取完成订单详情
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _getCompleteOrderDetail(Collection $orders)
    {
        $payType = cons('trade.pay_type');
        return [
            'count' => $orders->count(),
            'amount' => $orders->sum('after_rebates_price'),
            'onlineAmount' => $orders->filter(function ($item) use ($payType) {
                return !is_null($item->systemTradeInfo) && $item->systemTradeInfo->pay_type != $payType['pos'];
            })->sum('after_rebates_price'),
            'offAmount' => $orders->filter(function ($item) {
                return is_null($item->systemTradeInfo);
            })->sum('after_rebates_price'),
            'posAmount' => $orders->filter(function ($item) use ($payType) {
                return !is_null($item->systemTradeInfo) && $item->systemTradeInfo->pay_type == $payType['pos'];
            })->sum('after_rebates_price')
        ];
    }

    /**
     * 获取排行数据
     *
     * @param $data
     * @param $beginDay
     * @param $dayEnd
     * @return array
     */
    private function _getRankData($data, $beginDay, $dayEnd)
    {
        $orderConfig = cons('order');
        $userType = array_get($data, 'type');
        //符合条件的订单商品
        $orderGoods = OrderGoods::whereHas('order', function ($query) use ($orderConfig) {
            $query->where('is_cancel', $orderConfig['is_cancel']['off'])
                ->where('status', '<>', $orderConfig['status']['invalid']);
        })
            ->whereBetween('created_at', [$beginDay, $dayEnd])
            ->groupBy('goods_id')
            ->select(DB::raw('sum(total_price) as amount, goods_id'))
            ->orderBy('amount', 'DESC')->get('amount', 'goods_id');

        //符合条件的商品
        $goods = Goods::withTrashed()->whereIn('id',
            $orderGoods->pluck('goods_id'))->ofDeliveryArea($data)->ofUserType($userType)->select('id',
            'name', 'cate_level_1', 'cate_level_2',
            'cate_level_3')->get()->keyBy('id');
        $goodsIds = $goods->keys('id')->all();

        $orderGoods = $orderGoods->filter(function ($item) use ($goodsIds) {
            return in_array($item->goods_id, $goodsIds);
        })->take(10);


        //符合条件的店铺
        $shops = Shop:: ofUserType($userType)->ofDeliveryArea($data, 'shopAddress')->whereHas('Orders',
            function ($query) use ($beginDay, $dayEnd) {
                $query->whereBetween('created_at', [$beginDay, $dayEnd]);
            })->get(['id', 'name'])->keyBy('id');

        $shopIds = $shops->keys('id');

        $shopAmount = Order::whereIn('shop_id', $shopIds)
            ->where(function ($query) use ($orderConfig) {
                $query->where('is_cancel', $orderConfig['is_cancel']['off'])
                    ->where('status', '<>', $orderConfig['status']['invalid']);
            })
            ->whereBetween('created_at', [$beginDay, $dayEnd])
            ->groupBy('shop_id')
            ->select(DB::raw('sum(price) as amount, shop_id'))
            ->orderBy('amount', 'DESC')->take(10)->get('amount', 'shop_id');

        return compact('orderGoods', 'goods', 'shops', 'shopAmount');
    }

    /**
     * 获取table
     *
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     * @param array $titles
     * @return \PhpOffice\PhpWord\Element\Table
     */
    private function _getTable(PhpWord $phpWord, $titles)
    {
        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        $section = $phpWord->addSection($style);
        $table = $section->addTable('table');


        $table->addRow();
        foreach ($titles as $name => $width) {
            $table->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true],
                array('align' => 'center'));
        }
        return $table;
    }

    /**
     * 数据填充
     *
     * @param $table
     * @param $data
     */
    private function _fillData($table, $data)
    {
        $cellAlignCenter = array('align' => 'center');
        foreach ($data as $items) {
            $table->addRow();
            foreach ($items as $key => $item) {
                if (is_array($item)) {
                    $table->addCell(0, $item)->addText($key, null, $cellAlignCenter);
                } else {
                    $table->addCell()->addText($item, null, $cellAlignCenter);
                }
            }
        }
    }

    /**
     * 获取销售商品数据
     *
     * @param $data
     * @param $beginDay
     * @param $dayEnd
     * @return mixed
     */
    private function _getGoodsSalesData($data, $beginDay, $dayEnd)
    {
        $where = array_filter(array_only($data, ['user_type']));
        $q = array_get($data, 'q');

        $name = null;
        is_numeric($q) ? ($where['bar_code'] = $q) : ($name = $q);

        $orderConfig = cons('order');

        //符合条件的订单商品
        $orderGoodsTemp = OrderGoods::whereHas('order', function ($query) use ($orderConfig) {
            $query->where('is_cancel', $orderConfig['is_cancel']['off'])
                ->where('status', '<>', $orderConfig['status']['invalid']);
        })->whereBetween('created_at', [$beginDay, $dayEnd])
            ->groupBy('goods_id')
            ->select(DB::raw('sum(total_price) as amount,sum(num) as count, goods_id'))
            ->get();

        //符合条件的商品
        $goods = Goods::withTrashed()->whereIn('id',
            $orderGoodsTemp->pluck('goods_id'))->where($where)->ofGoodsName($name)->ofDeliveryArea($data)->with('shop')->select('id',
            'name', 'cate_level_1', 'cate_level_2', 'cate_level_3', 'bar_code',
            'shop_id', 'user_type')->get()->keyBy('id');

        $goodsIds = $goods->keys('id')->all();

        $orderGoods = $orderGoodsTemp->filter(function ($item) use ($goodsIds) {
            return in_array($item->goods_id, $goodsIds);
        });

        return compact('orderGoods', 'goods');
    }

    /**
     * 订单按卖家分组
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _orderGroupBySeller(Collection $orders)
    {
        $userTypes = cons('user.type');

        $supplierOrders = $orders->filter(function ($order) use ($userTypes) {
            return $order->shop_user_type == $userTypes['supplier'];
        });

        $supplierOrdersBuyByWholesaler = $supplierOrders->filter(function ($item) {
            return $item->user_type_name == 'wholesaler';
        });

        $supplierOrdersBuyByRetailer = $supplierOrders->reject(function ($item) {
            return $item->user_type_name == 'wholesaler';
        });

        $wholesalerOrders = $orders->reject(function ($order) use ($userTypes) {
            return $order->shop_user_type == $userTypes['supplier'];
        });

        return compact('supplierOrdersBuyByWholesaler', 'supplierOrdersBuyByRetailer', 'wholesalerOrders');
    }

    /**
     * 订单按买家分组
     *
     * @param \Illuminate\Support\Collection $orders
     * @return array
     */
    private function _orderGroupByBuyer(Collection $orders)
    {
        //终端商下单
        $retailerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'retailer';
        });

        //批发商下单
        $wholesalerOrders = $orders->filter(function ($item) {
            return $item->user_type_name == 'wholesaler';
        });
        return compact('retailerOrders', 'wholesalerOrders');
    }
}
