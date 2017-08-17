<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\SalesmanCustomerDisplayList;
use App\Models\SalesmanVisit;
use App\Models\SalesmanVisitGoodsRecord;
use App\Models\SalesmanVisitOrder;
use App\Models\SalesmanVisitOrderGoods;
use App\Services\BusinessService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Index\Controller;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use PhpOffice\PhpWord\PhpWord;

class ReportController extends Controller
{
    /**
     * 报告首页
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        $startDate = $request->input('start_date', ((new Carbon)->startOfMonth()->toDateString()));
        $endDate = $request->input('end_date', ((new Carbon)->toDateString()));
        $endDateTemp = (new Carbon($endDate))->addDay()->toDateString();

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders($shop, $startDate, $endDateTemp);
        return view('index.business.report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesmen' => $salesmenOrderData
        ]);
    }

    /**
     * 业务员报告详情
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, $salesmanId)
    {
        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $carbon = new Carbon();
        $data = $request->all();

        $date = $carbon->copy()->toDateString();
        //开始时间
        $startDate = array_get($data, 'start_date', $date);
        //结束时间
        $endDate = array_get($data, 'end_date', $date);

        $dateEnd = (new Carbon($endDate))->endOfDay();

        //拜访记录
        $visits = $salesman->visits()->ofTime($startDate, $dateEnd)->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();
        $visitOrders = $salesman->orders()->ofData([
            'start_date' => $startDate,
            'end_date' => $dateEnd,
        ])->with(['order.orderGoods.goods', 'salesmanCustomer', 'order.coupon'])->get();
        return view('index.business.report-detail',
            array_merge($this->_getVisitData($visits, $visitOrders), compact('startDate', 'endDate', 'salesman')));
    }

    /**
     * 客户统计详情
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function customerDetail(Request $request, $salesmanId)
    {
        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $carbon = new Carbon();
        $data = $request->all();

        //客户id
        $customerId = array_get($data, 'customer_id');
        if (!$customerId) {
            return $this->error('客户不存在');
        }

        //开始时间
        $startDate = array_get($data, 'start_date', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endDate = array_get($data, 'end_date', $carbon->copy()->toDateString());

        $dateEnd = (new Carbon($endDate))->endOfDay();

        $visits = SalesmanVisit::ofTime($startDate, $dateEnd)->where([
            'salesman_id' => $salesmanId,
            'salesman_customer_id' => $customerId,
        ])->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'orders.applyPromo.promo.promoContent',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress',
        ])->get();
        return view('index.business.report-customer-detail',
            array_merge(compact('salesmanId', 'customerId', 'startDate', 'endDate'), $this->_getDetailData($visits)));
    }

    /**
     * 拜访数据
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @return array
     */
    private function _getVisitData(Collection $visits, Collection $orders)
    {
        $user = auth()->user();
        $orderTypes = cons('salesman.order.type');
        $userType = cons('user.type');
        //订货单
        $visitOrders = $orders->filter(function ($order) use ($orderTypes) {
            return ($order->type == $orderTypes['order'] && $order->salesman_visit_id > 0);
        });
        //退货单
        $returnOrders = $orders->filter(function ($order) use ($orderTypes) {
            return $order->type == $orderTypes['return_order'];
        });
        //自主订货单
        $ownOrders = $orders->filter(function ($order) use ($orderTypes, $user, $userType) {
            $customerIdentity = true;
            if ($user->type == $userType['maker']) {
                $customerIdentity = ($order->salesmanCustomer->type == $userType['supplier']);
            }
            return $customerIdentity && $order->type == $orderTypes['order'] && $order->salesman_visit_id == 0 && $order->order && $order->order->user_id != $user->id;
        });

        $customerIds = $visits->pluck('salesman_customer_id')->toBase()->unique();

        $ownOrder = $ownOrders->filter(function ($order) {
            return (isset($order->order) ? ($order->order->status < cons('order.status.invalid') && $order->order->pay_status < cons('order.pay_status.refund')) : true);
        });

        $visitOrderAmount = $visitOrders->filter(function ($order) {
            return (isset($order->order) ? ($order->order->status < cons('order.status.invalid') && $order->order->pay_status < cons('order.pay_status.refund')) : true);
        });
        $visitStatistics = [
            'customerCount' => $customerIds->count(),
            'returnOrderCount' => $returnOrders->count(),
            'returnOrderAmount' => $returnOrders->sum('amount'),
            'visitOrderCount' => $visitOrders->count(),
            'visitOrderAmount' => $visitOrderAmount->sum('amount'),
            'ownOrderCount' => $ownOrders->count(),
            'ownOrderAmount' => $ownOrder->sum('amount'),
            'totalCount' => bcadd($visitOrders->count(), $ownOrders->count()),
            'totalAmount' => bcadd($visitOrderAmount->sum('amount'), $ownOrder->sum('amount'), 2),
            'ownOrderDiscountAmount' => $ownOrder->sum('how_much_discount'),
            'visitOrderDiscountAmount' => $visitOrderAmount->sum('how_much_discount'),
        ];
        $visitList = [];
        foreach ($customerIds as $customerId) {
            $visitList[] = $this->_getVisitList($visits, $customerId);
        }

        return compact('visitStatistics', 'visitList', 'ownOrders');
    }

    /**
     * 详情数据
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @return array
     */
    private function _getDetailData(Collection $visits)
    {
        //详情拜访列表

        $visitLists = $this->_getVisitListForDetail($visits);

        $visits = $visits->filter(function ($visit) {
            return isset($visit->orders[0]->order) ? ($visit->orders[0]->order->status < cons('order.status.invalid')) : true;
        });
        //销售统计
        $salesGoods = $this->_getSalesGoods($visits);

        //陈列费

        $displays = $this->_getDisplay($visits);

        //赠品
        $gifts = $this->_getGifts($visits);

        //促销
        $promos = $this->_getPromos($visits);

        return compact('visitLists', 'salesGoods', 'displays', 'gifts', 'promos');
    }

    /**
     * 获取赠品
     *
     * @param $visits
     * @return array
     */
    public function _getGifts($visits)
    {
        $orderIds = $visits->pluck('orders')->collapse()->pluck('id');
        $orders = SalesmanVisitOrder::whereIn('id', $orderIds)->get();
        $data = [];
        foreach ($orders as $order) {
            if (!$order->gifts->isEmpty()) {
                foreach ($order->gifts as $goods) {
                    $data[] = [
                        'time' => $order->created_at,
                        'goods_name' => $goods->name,
                        'num' => $goods->pivot->num . cons()->valueLang('goods.pieces', $goods->pivot->pieces)
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * 获取促销
     *
     * @param $visits
     * @return array
     */
    public function _getPromos($visits)
    {

        $orderIds = $visits->pluck('orders')->collapse()->pluck('id');
        $orders = SalesmanVisitOrder::with('applyPromo.promo.promoContent')->whereIn('id', $orderIds)->get();
        $data = [];
        foreach ($orders as $order) {
            if (!is_null($order->promo)) {
                $order->promo->time = $order->created_at;
                $data[] = $order->promo;
            }
        }
        return $data;
    }

    /**
     * 获取陈列
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @return array
     */
    private function _getDisplay(Collection $visits)
    {
        $orderIds = $visits->pluck('orders')->collapse()->pluck('id');
        $displays = SalesmanCustomerDisplayList::whereIn('salesman_visit_order_id', $orderIds)->get();

        $data = [];

        foreach ($displays as $display) {
            $data[] = [
                'time' => $display->created_at,
                'month' => $display->month,
                'name' => $display->mortgage_goods_name,
                'used' => $display->mortgage_goods_id ? (int)$display->used . cons()->valueLang('goods.pieces',
                        $display->mortgage_goods_pieces) : $display->used,
            ];
        }
        return $data;

    }

    /**
     * 销售商品统计
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @return array
     */
    private function _getSalesGoods(Collection $visits)
    {
        //拜访id列表
        $visitIds = $visits->pluck('id');
        //商品记录
        $goodsRecords = SalesmanVisitGoodsRecord::whereIn('salesman_visit_id', $visitIds)->get();

        //销售商品
        $salesGoods = SalesmanVisitOrderGoods::whereIn('salesman_visit_id', $visitIds)->get();

        $goodsIds = $salesGoods->pluck('goods_id')->toBase()->unique();


        $salesGoodsLists = [];
        foreach ($goodsIds as $goodsId) {
            $salesGoodsLists[$goodsId] = $this->_salesGoodsStatistics($salesGoods, $goodsRecords, $goodsId);
        }

        //有访问无订单的商品
        $recordGoods = $goodsRecords->filter(function ($item) use ($goodsIds) {
            return !$goodsIds->contains($item->goods_id);
        });

        foreach ($recordGoods as $goods) {
            $salesGoodsLists[$goods->goods_id] = [
                'id' => $goods->goods_id,
                'name' => $goods->goods_name,
                'stock' => $goods->stock,
                'productionDate' => $goods->production_date,
                'returnCount' => 0,
                'returnAmount' => 0,
                'count' => 0,
                'amount' => 0,
                'pieces' => [['amount' => 0, 'num' => 0]]
            ];
        }
        return $salesGoodsLists;
    }

    /**
     * 销售商品数据
     *
     * @param \Illuminate\Database\Eloquent\Collection $salesGoods
     * @param \Illuminate\Database\Eloquent\Collection $goodsRecords
     * @param $goodsId
     * @return array
     */
    private function _salesGoodsStatistics(Collection $salesGoods, Collection $goodsRecords, $goodsId)
    {

        $config = cons('salesman.order.goods.type');
        //所有商品
        $salesGoods = $salesGoods->filter(function ($item) use ($goodsId) {
            return $item->goods_id == $goodsId && (isset($item->order) ? $item->order->status < cons('order.status.invalid') : true);
        });

        //订货商品
        $orderGoods = $salesGoods->filter(function ($item) use ($config) {
            return $item->type == $config['order'];
        });
        //退货商品
        $returnGoods = $salesGoods->reject(function ($item) use ($config) {
            return $item->type == $config['order'];
        });

        $firstGoods = $salesGoods->first();
        $lastRecord = $goodsRecords->last(function ($key, $record) use ($goodsId) {
            return $record->goods_id == $goodsId;
        });

        $data = [
            'id' => $goodsId,
            'name' => $firstGoods->goods_name,
            'stock' => $lastRecord ? $lastRecord->stock : '- -',
            'productionDate' => $lastRecord ? $lastRecord->production_date : '- -',
            'returnCount' => $returnGoods->sum('num'),
            'returnAmount' => $returnGoods->sum('amount'),
            'count' => $orderGoods->sum('num'),
            'amount' => $orderGoods->sum('amount'),
        ];
        if (!$orderGoods->isEmpty()) {
            //平均
            foreach ($orderGoods as $item) {
                $pieces = $item->pieces;
                $data['pieces'][$pieces] ['amount'] = isset($data['pieces'][$pieces]['amount']) ? bcadd($data['pieces'][$pieces]['amount'],
                    $item->amount) : $item->amount;
                $data['pieces'][$pieces] ['num'] = isset($data['pieces'][$pieces]['num']) ? bcadd($data['pieces'][$pieces]['num'],
                    $item->num) : $item->num;
            }
        } else {
            $data['pieces'][] = [
                'amount' => 0,
                'num' => 0
            ];
        }

        return $data;
    }

    /**
     * 详情拜访列表
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @return array
     */
    private function _getVisitListForDetail(Collection $visits, $photos = true)
    {
        //拜访列表
        $visitLists = [];
        foreach ($visits as $visit) {
            //订单数
            $orders = $visit->orders;

            $orderTypes = cons('salesman.order.type');
            //订货单
            $visitOrder = $orders->filter(function ($order) use ($orderTypes) {
                return $order->type == $orderTypes['order'];
            })->first();
            //退货单
            $returnOrder = $orders->filter(function ($order) use ($orderTypes) {
                return $order->type == $orderTypes['return_order'];
            })->first();
            $visitList = [
                'time' => $visit->created_at,
                'commitAddress' => $visit->address,
                'orderAmount' => ($visitOrder ? $visitOrder->amount . ($visitOrder->order ? ($visitOrder->order->status < cons('order.status.invalid') ? '' : '(订单已作废)') : '') : 0),
                'returnAmount' => ($returnOrder ? $returnOrder->amount : 0),
                'hasDisplay' => ($visitOrder && count($visitOrder->displayList) != 0) ? '有' : '无',
            ];
            $photos ? $visitList['photos'] = $visit->photos_url : false;
            $visitLists[] = $visitList;

        }
        return $visitLists;
    }

    /**
     * 拜访列表
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits
     * @param $customerId
     * @return array
     */
    private function _getVisitList(Collection $visits, $customerId)
    {
        //客户拜访
        $customerVisits = $visits->filter(function ($visit) use ($customerId) {
            return $visit->salesman_customer_id == $customerId;
        });

        $lastVisit = $customerVisits->first();

        //客户信息
        $salesmanCustomer = $lastVisit->salesmanCustomer;

        //订单数
        $orders = $customerVisits->pluck('orders')->collapse();

        $orderTypes = cons('salesman.order.type');
        //订货单
        $visitOrders = $orders->filter(function ($order) use ($orderTypes) {
            return $order->type == $orderTypes['order'] && (isset($order->order) ? $order->order->status < cons('order.status.invalid') : true);
        });

        //退货单
        $returnOrders = $orders->filter(function ($order) use ($orderTypes) {
            return $order->type == $orderTypes['return_order'];
        });

        return [
            'id' => $customerId,
            'name' => $salesmanCustomer ? $salesmanCustomer->name : '',
            'contact' => $salesmanCustomer ? $salesmanCustomer->contact : '',
            'contactInfo' => $salesmanCustomer ? $salesmanCustomer->contact_information : '',
            'businessAddress' => $salesmanCustomer ? $salesmanCustomer->business_address_name : '',
            'business_address_lng' => $salesmanCustomer->business_address_lng,
            'business_address_lat' => $salesmanCustomer->business_address_lat,
            'lng' => $lastVisit->x_lng,
            'lat' => $lastVisit->y_lat,
            'visit_id' => $lastVisit->id,
            'commitAddress' => $lastVisit->address,
            'visitTime' => $lastVisit->created_at,
            'visitCount' => $customerVisits->count(),
            'orderCount' => $visitOrders->count(),
            'orderAmount' => $visitOrders->sum('amount'),
            'returnOrderCount' => $returnOrders->count(),
            'returnOrderAmount' => $returnOrders->sum('amount'),
        ];

    }

    /**
     * 报告详情导出
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function export(Request $request, $salesmanId)
    {
        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $carbon = new Carbon();
        $data = $request->all();
        //开始时间
        $startDate = array_get($data, 'start_date', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endDate = array_get($data, 'end_date', $carbon->copy()->toDateString());

        $dateEnd = (new Carbon($endDate))->endOfDay();

        //拜访记录
        $visits = $salesman->visits()->ofTime($startDate, $dateEnd)->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();

        $visitOrders = $salesman->orders()->ofData([
            'start_date' => $startDate,
            'end_date' => $dateEnd,
        ])->with(['order.orderGoods.goods', 'salesmanCustomer', 'order.coupon',])->get();

        extract($this->_getVisitData($visits, $visitOrders));
        $excelName = $startDate . '-' . $endDate . ' ' . $salesman->name . '业务报表';

        $isDay = $startDate == $endDate;
        if ($isDay) {
            $forget = ['business_address_lng', 'business_address_lat', 'lng', 'lat', 'visit_id'];
        } else {
            $forget = [
                'business_address_lng',
                'business_address_lat',
                'lng',
                'lat',
                'visit_id',
                'commitAddress',
                'visitTime'
            ];
        }
        foreach ($visitList as $key => $item) {
            array_forget($visitList[$key], $forget);
        }
        Excel::create($excelName,
            function (LaravelExcelWriter $excel) use ($visitStatistics, $visitList, $ownOrders, $isDay) {


                $excel->sheet('总计', function (LaravelExcelWorksheet $sheet) use ($visitStatistics) {

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
                        'J' => 20,
                    ));

                    //标题
                    $titles = [
                        '拜访客户数',
                        '总订货单数',
                        '退货金额',
                        '拜访订货单数',
                        '拜访订货金额',
                        '自主订货单数',
                        '自主订货金额',
                        '退货总单数',
                        '总订货金额',
                        '总应付金额',
                    ];
                    $visitStatistics['amountPayable'] = bcsub($visitStatistics['totalAmount'],
                        $visitStatistics['ownOrderDiscountAmount'] + $visitStatistics['visitOrderDiscountAmount'], 2);
                    unset(
                        $visitStatistics['visitOrderDiscountAmount'],
                        $visitStatistics['ownOrderDiscountAmount']
                    );
                    $sheet->rows([$titles, $visitStatistics]);

                    //单元格居中
                    $sheet->cells('A1:J2', function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });


                $excel->sheet('拜访总计', function (LaravelExcelWorksheet $sheet) use ($visitList, $isDay) {
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);
                    if ($isDay) {
                        // 设置宽度
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 10,
                            'D' => 15,
                            'E' => 40,
                            'F' => 40,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                            'K' => 20,
                            'L' => 20
                        ));

                        //标题
                        $titles = [
                            '客户编号',
                            '店铺名称',
                            '联系人',
                            '联系电话',
                            '营业地址',
                            '提交地址',
                            '拜访时间',
                            '拜访次数',
                            '订货单数',
                            '订货总金额',
                            '退货单数',
                            '退货总金额'
                        ];
                    } else {
                        // 设置宽度
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 10,
                            'D' => 15,
                            'E' => 40,
                            'F' => 20,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                        ));

                        //标题
                        $titles = [
                            '客户编号',
                            '店铺名称',
                            '联系人',
                            '联系电话',
                            '营业地址',
                            '拜访次数',
                            '订货单数',
                            '订货总金额',
                            '退货单数',
                            '退货总金额'
                        ];
                    }

                    $data = array_merge([$titles], $visitList);

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:L' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });

                $excel->sheet('自主订单', function (LaravelExcelWorksheet $sheet) use ($ownOrders) {

                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 20,
                        'C' => 20,
                        'D' => 10,
                        'E' => 20,
                        'F' => 10,
                    ));
                    //标题
                    $titles = [
                        '客户编号',
                        '客户名称',
                        '同步时间',
                        '订单ID',
                        '订单状态',
                        '订单金额',
                    ];

                    $data = [$titles];


                    foreach ($ownOrders as $ownOrder) {
                        $data[] = [
                            $ownOrder->salesman_customer_id,
                            $ownOrder->customer_name,
                            $ownOrder->created_at,
                            $ownOrder->order_id,
                            $ownOrder->order_status_name,
                            $ownOrder->amount
                        ];
                    }

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:F' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });
            })->export('xls');

    }


    /**
     * 客户详情导出
     *
     * @param \Illuminate\Http\Request $request
     * @param $salesmanId
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function exportCustomerDetail(Request $request, $salesmanId)
    {

        $shop = auth()->user()->shop;
        $salesman = $shop->salesmen()->find($salesmanId);
        if (is_null($salesman)) {
            return $this->error('业务员不存在');
        }
        $carbon = new Carbon();
        $data = $request->all();
        //客户id
        $customerId = array_get($data, 'customer_id');
        if (!$customerId) {
            return $this->error('客户不存在');
        }
        $customer = $shop->salesmenCustomer()->find($customerId);
        if (!$customer) {
            return $this->error('客户不存在');
        }
        //开始时间
        $startDate = array_get($data, 'start_date', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endDate = array_get($data, 'end_date', $carbon->copy()->toDateString());

        $dateEnd = (new Carbon($endDate))->endOfDay();

        //拜访记录
        $visits = $salesman->visits()->ofTime($startDate, $dateEnd)->where('salesman_customer_id', $customerId)->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();

        $visits = $visits->filter(function ($visit) {
            return isset($visit->orders[0]->order) ? ($visit->orders[0]->order->status < cons('order.status.invalid')) : true;
        });
        $visitsLists = $this->_getVisitListForDetail($visits);
        $visitsList = [];
        foreach ($visitsLists as $visit) {
            $visitsList[] = [
                $visit['time'],
                $visit['commitAddress'],
                $visit['orderAmount'],
                $visit['returnAmount'],
                $visit['hasDisplay'],
            ];
        }
        //销售统计
        $salesGoods = $this->_getSalesGoods($visits);

        //陈列费
        $displays = $this->_getDisplay($visits);
        //赠品
        $gifts = $this->_getGifts($visits);
        //促销
        $promos = $this->_getPromos($visits);

        $excelName = $startDate . '-' . $endDate . ' ' . $customer->name . '(' . $salesman->name . ')业务报表';;
        Excel::create($excelName,
            function (LaravelExcelWriter $excel) use ($visitsList, $salesGoods, $displays, $gifts, $promos) {
                $excel->sheet('拜访记录', function (LaravelExcelWorksheet $sheet) use ($visitsList) {
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 15,
                        'B' => 30,
                        'C' => 10,
                        'D' => 10,
                        'E' => 15
                    ));

                    //标题
                    $titles = [
                        '拜访时间',
                        '提交地址',
                        '订货金额',
                        '退货金额',
                        '陈列费'
                    ];
                    $data = array_merge([$titles], $visitsList);

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:E1', function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });
                $excel->sheet('销售总计', function (LaravelExcelWorksheet $sheet) use ($salesGoods) {

                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 20,
                        'C' => 10,
                        'D' => 15,
                        'E' => 40,
                        'F' => 20,
                        'G' => 20,
                        'H' => 20,
                        'I' => 20,
                        'J' => 20,
                    ));

                    //标题
                    $titles = [
                        '商品ID',
                        '商品名称',
                        '库存',
                        '生产日期',
                        '退货数量',
                        '退货金额',
                        '订货总数量',
                        '订货总金额',
                        '平均单价',
                        '订货数量'
                    ];
                    //合并
                    $mergeArray = [];
                    $goods = [];
                    //dd($salesGoods);
                    foreach ($salesGoods as $item) {
                        $start = count($goods) + 2;
                        $count = 0;
                        $mergeArray[$start] = [$start, $start + count($item['pieces']) - 1];
                        foreach ($item['pieces'] as $piece => $value) {
                            $perice = ($value['num'] ? number_format(bcdiv($value['amount'], $value['num'], 2),
                                    2) : 0) . '/' . cons()->valueLang('goods.pieces', $piece);
                            if ($count == 0) {
                                $goods[] = [
                                    $item['id'],
                                    $item['name'],
                                    $item['stock'],
                                    $item['productionDate'],
                                    $item['returnCount'],
                                    $item['returnAmount'],
                                    $item['count'],
                                    $item['amount'],
                                    $perice,
                                    $value['num']
                                ];
                            } else {
                                $goods[] = [
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $perice,
                                    $value['num']
                                ];
                            }
                            $count++;
                        }
                    }
                    $data = array_merge([$titles], $goods);
                    $sheet->rows($data);
                    $sheet->setMergeColumn(array(
                        'columns' => array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'),
                        'rows' => $mergeArray
                    ));
                    //单元格居中
                    $sheet->cells('A1:J' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });
                $excel->sheet('陈列费', function (LaravelExcelWorksheet $sheet) use ($displays) {

                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 20,
                        'C' => 20,
                        'D' => 10
                    ));

                    //标题
                    $titles = [
                        '拜访时间',
                        '月份',
                        '名称',
                        '数量/金额'
                    ];
                    $data = array_merge([$titles], $displays);

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:F' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });

                });
                $excel->sheet('赠品', function (LaravelExcelWorksheet $sheet) use ($gifts) {
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 20,
                        'B' => 70,
                        'C' => 10
                    ));

                    //标题
                    $titles = [
                        '拜访时间',
                        '名称',
                        '数量',
                    ];
                    $data = array_merge([$titles], $gifts);

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:C' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                });

                $excel->sheet('促销活动', function (LaravelExcelWorksheet $sheet) use ($promos) {

                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                    // 设置宽度
                    $sheet->setWidth(array(
                        'A' => 30,
                        'B' => 30,
                        'C' => 50,
                        'D' => 70,
                        'E' => 30,
                    ));
                    /* $sheet->mergeCells('C1:E1', true);
                     $sheet->mergeCells('F1:H1');*/
                    //标题
                    $titles = [
                        '拜访时间',
                        '促销活动名称',
                        '有效时间',
                        '返利结果',
                        '备注',
                    ];
                    //合并
                    $goods = [];
                    $mergeArray = [];
                    foreach ($promos as $item) {
                        $start = count($goods) + 2;
                        $count = 0;
                        $mergeArray[] = [$start, $start + count($item['rebate']) - 1];
                        $record = [
                            $item['time'],
                            $item['name'],
                            $item['start_at'] . '-' . $item['end_at'],
                        ];
                        foreach ($item['rebate'] as $rebate) {
                            if ($count == 0) {
                                switch ($item['type']) {
                                    case cons('promo.type.custom'):
                                        $record[] = $rebate['custom'];
                                        break;
                                    case cons('promo.type.money-money'):
                                        $record[] = $rebate['money'];
                                        break;
                                    case cons('promo.type.money-goods'):
                                        $record[] = $rebate->goods->name . 'x' . $rebate->quantity . cons()->valueLang('goods.pieces',
                                                $rebate->unit);
                                        break;
                                    case cons('promo.type.goods-money'):
                                        $record[] = $rebate['money'];
                                        break;
                                    case cons('promo.type.goods-goods'):
                                        $record[] = $rebate->goods->name . 'x' . $rebate->quantity . cons()->valueLang('goods.pieces',
                                                $rebate->unit);
                                        break;
                                }
                                $record[] = $item->remark;
                            } else {
                                $record = [
                                    '',
                                    '',
                                    '',
                                    $record[] = $rebate->goods->name . 'x' . $rebate->quantity . cons()->valueLang('goods.pieces',
                                            $rebate->unit),
                                    ''
                                ];
                            }
                            $count++;
                            $goods[] = $record;
                        }
                    }
                    $data = array_merge([$titles], $goods);
                    $sheet->rows($data);

                    $sheet->setMergeColumn(array(
                        'columns' => array('A', 'B', 'C', 'E'),
                        'rows' => $mergeArray
                    ));
                    //单元格居中
                    $sheet->cells('A1:E' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                });
            })->export('xls');
    }

    /**
     * 报告列表导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function exportIndex(Request $request)
    {
        $shop = auth()->user()->shop;
        $startDate = $request->input('start_date', ((new Carbon)->startOfMonth()->toDateString()));
        $endDate = $request->input('end_date', ((new Carbon)->toDateString()));
        $endDateTemp = (new Carbon($endDate))->addDay()->toDateString();

        $salesmenOrderData = (new BusinessService())->getSalesmanOrders($shop, $startDate, $endDateTemp);

        //客户列表

        //拜访记录
        $visits = SalesmanVisit::whereIn('salesman_id', $salesmenOrderData->pluck('id'))->ofTime($startDate,
            $endDateTemp)->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.address',
            'salesmanCustomer.salesman'
        ])->get();

        $customerIds = $visits->pluck('salesman_customer_id')->toBase()->unique();

        $visitList = [];
        foreach ($customerIds as $customerId) {
            $visitList[] = $this->_getVisitList($visits, $customerId);
        }

        $excelName = $startDate . '至' . $endDate . '业务报表';

        $isDay = $startDate == $endDate;
        if ($isDay) {
            $forget = ['business_address_lng', 'business_address_lat', 'lng', 'lat', 'visit_id'];
        } else {
            $forget = [
                'business_address_lng',
                'business_address_lat',
                'lng',
                'lat',
                'visit_id',
                'commitAddress',
                'visitTime'
            ];
        }

        //业务员
        $salesmanCustomer = $visits->pluck('salesmanCustomer')->toBase()->unique()->keyBy('id')->toArray();

        foreach ($visitList as $key => $item) {
            $visitList[$key][key($item)] = $salesmanCustomer[$item['id']]['salesman']['name'];
            array_forget($visitList[$key], $forget);
        }
        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($salesmenOrderData, $visitList, $isDay) {
            $excel->sheet('业务报表', function (LaravelExcelWorksheet $sheet) use ($salesmenOrderData) {

                // Set auto size for sheet
                $sheet->setAutoSize(true);

                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 15,
                    'C' => 20,
                    'D' => 25,
                    'E' => 15,
                    'F' => 15,
                    'G' => 15,
                    'H' => 15,
                    'I' => 15,
                ));

                //标题
                $titles = [
                    '业务员',
                    '拜访客户数',
                    '订货单数(拜访+自主)',
                    '订货总金额(拜访+自主)',
                    '已配送单数',
                    '已完成金额',
                    '未完成金额',
                    '退货单数',
                    '退货总金额'
                ];
                $sheet->appendRow($titles);
                foreach ($salesmenOrderData as $man) {
                    $sheet->appendRow([
                        $man->name,
                        $man->visitCustomerCount,
                        $man->orderFormCount . '(' . $man->visitOrderFormCount . '+' . bcsub($man->orderFormCount,
                            $man->visitOrderFormCount) . ')',
                        $man->orderFormSumAmount . '(' . $man->visitOrderFormSumAmount . '+' . ($man->orderFormSumAmount - $man->visitOrderFormSumAmount) . ')',
                        $man->deliveryFinishCount,
                        $man->finishedAmount,
                        $man->notFinishedAmount,
                        $man->returnOrderCount,
                        $man->returnOrderSumAmount
                    ]);
                }

                //单元格居中
                $sheet->cells('A1:I' . (count($salesmenOrderData) + 1), function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

            });
            $excel->sheet('客户', function (LaravelExcelWorksheet $sheet) use ($visitList, $isDay) {
                if (!empty($visitList)) {
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);
                    if ($isDay) {
                        // 设置宽度
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 10,
                            'D' => 15,
                            'E' => 40,
                            'F' => 40,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                            'K' => 20,
                            'L' => 20
                        ));

                        //标题
                        $titles = [
                            '业务员',
                            '店铺名称',
                            '联系人',
                            '联系电话',
                            '营业地址',
                            '提交地址',
                            '拜访时间',
                            '拜访次数',
                            '订货单数',
                            '订货总金额',
                            '退货单数',
                            '退货总金额'
                        ];
                    } else {
                        // 设置宽度
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 10,
                            'D' => 15,
                            'E' => 40,
                            'F' => 20,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                        ));

                        //标题
                        $titles = [
                            '业务员',
                            '店铺名称',
                            '联系人',
                            '联系电话',
                            '营业地址',
                            '拜访次数',
                            '订货单数',
                            '订货总金额',
                            '退货单数',
                            '退货总金额'
                        ];
                    }

                    $data = array_merge([$titles], $visitList);

                    $sheet->rows($data);

                    //单元格居中
                    $sheet->cells('A1:L' . count($data), function (CellWriter $cells) {
                        $cells->setAlignment('center');
                        $cells->setValignment('center');
                    });
                }
            });
        })->export('xls');
    }


}
