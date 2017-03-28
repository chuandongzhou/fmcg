<?php

namespace App\Http\Controllers\Index\Business;

use App\Models\SalesmanCustomerDisplayList;
use App\Models\SalesmanVisit;
use App\Models\SalesmanVisitGoodsRecord;
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
            'end_date' => $dateEnd
        ])->with(['order.orderGoods.goods', 'order.coupon'])->get();
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
            'salesman_customer_id' => $customerId
        ])->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();


        return view('index.business.report-customer-detail', array_merge(compact('salesmanId','customerId', 'startDate', 'endDate'),$this->_getDetailData($visits)));
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

        $orderTypes = cons('salesman.order.type');
        //订货单
        $visitOrders = $orders->filter(function ($order) use ($orderTypes) {
            return $order->type == $orderTypes['order'] && $order->salesman_visit_id > 0;
        });

        //退货单
        $returnOrders = $orders->filter(function ($order) use ($orderTypes) {
            return $order->type == $orderTypes['return_order'];
        });

        //自主订货单
        $ownOrders = $orders->filter(function ($order) use ($orderTypes) {

            return $order->type == $orderTypes['order'] && $order->salesman_visit_id == 0;
        });


        $customerIds = $visits->pluck('salesman_customer_id')->toBase()->unique();

        $visitStatistics = [
            'customerCount' => $customerIds->count(),
            'returnOrderCount' => $returnOrders->count(),
            'returnOrderAmount' => $returnOrders->sum('amount'),
            'visitOrderCount' => $visitOrders->count(),
            'visitOrderAmount' => $visitOrders->sum('amount'),
            'ownOrderCount' => $ownOrders->count(),
            'ownOrderAmount' => $ownOrders->sum('after_rebates_price'),
            'totalCount' => bcadd($visitOrders->count(), $ownOrders->count()),
            'totalAmount' => bcadd($visitOrders->sum('amount'), $ownOrders->sum('amount'))
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

        //销售统计
        $salesGoods = $this->_getSalesGoods($visits);

        //陈列费

        $displays = $this->_getDisplay($visits);

        return compact('visitLists', 'salesGoods', 'displays');
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
                'used' => $display->mortgage_goods_id ? (int)$display->used . cons()->valueLang('goods.pieces', $display->mortgage_goods_pieces) : $display->used,
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
            return $item->goods_id == $goodsId;
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
    private function _getVisitListForDetail(Collection $visits)
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

            $visitLists[] = [
                'time' => $visit->created_at,
                'commitAddress' => $visit->address,
                'orderAmount' => $visitOrder ? $visitOrder->amount : 0,
                'returnAmount' => $returnOrder ? $returnOrder->amount : 0,
                'hasDisplay' => ($visitOrder && count($visitOrder->displayList) != 0) ? '有' : '无'
            ];
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
            return $order->type == $orderTypes['order'];
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
            'end_date' => $dateEnd
        ])->with(['order.orderGoods.goods', 'order.coupon'])->get();

        extract($this->_getVisitData($visits, $visitOrders));

        $excelName = $startDate . '-' . $endDate . ' ' . $salesman->name . '业务报表';

        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($visitStatistics, $visitList, $ownOrders) {
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
                ));

                //标题
                $titles = [
                    '拜访客户数',
                    '总订货单数',
                    '总订货金额',
                    '拜访订货单数',
                    '拜访订货金额',
                    '自主订货单数',
                    '自主订货金额',
                    '退货总单数',
                    '退货金额',
                ];
                $sheet->rows([$titles, $visitStatistics]);

                //单元格居中
                $sheet->cells('A1:I2', function (CellWriter $cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });

            });
            $excel->sheet('拜访总计', function (LaravelExcelWorksheet $sheet) use ($visitList) {

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

                foreach ($visitList as $key => $item) {
                    array_forget($visitList[$key], [
                        'business_address_lng',
                        'business_address_lat',
                        'lng',
                        'lat',
                        'visit_id',
                        'commitAddress',
                        'visitTime'
                    ]);
                }
                $data = array_merge([$titles], $visitList);

                $sheet->rows($data);

                //单元格居中
                $sheet->cells('A1:J' . count($data), function (CellWriter $cells) {
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
        if (is_null($customer)) {
            return $this->error('客户不存在');
        }
        //开始时间
        $startDate = array_get($data, 'start_date', $carbon->copy()->startOfMonth()->toDateString());
        //结束时间
        $endDate = array_get($data, 'end_date', $carbon->copy()->toDateString());

        $dateEnd = (new Carbon($endDate))->endOfDay();

        $visits = SalesmanVisit::ofTime($startDate, $dateEnd)->where([
            'salesman_id' => $salesmanId,
            'salesman_customer_id' => $customerId
        ])->with([
            'orders.orderGoods.goods',
            'orders.displayList.mortgageGoods',
            'goodsRecord.goods',
            'salesmanCustomer.shippingAddress'
        ])->get();

        //详情拜访列表
        $visitLists = $this->_getVisitListForDetail($visits);

        //销售统计
        $salesGoods = $this->_getSalesGoods($visits);

        //陈列费
        $displays = $this->_getDisplay($visits);
        $excelName = $startDate . '-' . $endDate . ' ' . $customer->name . '(' . $salesman->name . ')业务报表';
        Excel::create($excelName, function (LaravelExcelWriter $excel) use ($visitLists, $salesGoods, $displays) {
            $excel->sheet('拜访记录', function (LaravelExcelWorksheet $sheet) use ($visitLists) {

                // Set auto size for sheet
                $sheet->setAutoSize(true);

                // 设置宽度
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 10,
                    'C' => 10,
                    'D' => 15,
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


                $data = array_merge([$titles], $visitLists);

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

        $phpWord = new PhpWord();

        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');


        $cellAlignCenter = ['align' => 'center'];
        $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center'];
        $cellRowContinue = array('vMerge' => 'continue');

        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(10);
        $phpWord->addTableStyle('table', $styleTable);

        $phpWord->addParagraphStyle('Normal', [
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'lineHeight' => 1.2,  // 行间距
        ]);

        $section = $phpWord->addSection();
        foreach ($salesmenOrderData as $man) {
            $table = $section->addTable('table');
            $table->addRow();
            $table->addCell(2000, $cellRowSpan)->addText($man->name, null, $cellAlignCenter);
            $table->addCell(1500)->addText('拜访客户数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货单数(拜访+自主)', null, $cellAlignCenter);
            $table->addCell(1500)->addText('订货总金额(拜访+自主)', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货单数', null, $cellAlignCenter);
            $table->addCell(1500)->addText('退货总金额', null, $cellAlignCenter);

            $table->addRow();
            $table->addCell(2000, $cellRowContinue);
            $table->addCell(1500)->addText($man->visitCustomerCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormCount . '(' . $man->visitOrderFormCount . '+' . bcsub($man->orderFormCount,
                    $man->visitOrderFormCount) . ')', null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->orderFormSumAmount . '(' . $man->visitOrderFormSumAmount . '+' . ($man->orderFormSumAmount - $man->visitOrderFormSumAmount) . ')',
                null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderCount, null, $cellAlignCenter);
            $table->addCell(1500)->addText($man->returnOrderSumAmount, null, $cellAlignCenter);
        }
        $name = $startDate . '至' . $endDate . '业务报表.docx';
        $phpWord->save(iconv('UTF-8', 'GBK//IGNORE', $name), 'Word2007', true);

    }


}
